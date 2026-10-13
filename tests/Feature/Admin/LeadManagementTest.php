<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->admin = Admin::create(['name' => 'Admin', 'email' => 'admin@gmail.com', 'password' => 'admin@123']);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function lead(array $attributes = []): Lead
    {
        return Lead::create([
            'name' => 'Riya Mehta',
            'phone' => '9876543210',
            'concern' => 'OCD Therapy',
            'mode' => 'In-clinic',
            ...$attributes,
        ]);
    }

    public function test_guests_cannot_see_leads(): void
    {
        $lead = $this->lead();

        $this->get('/secure-admin/leads')->assertRedirect('/secure-admin');
        $this->patch("/secure-admin/leads/{$lead->id}", ['status' => 'booked'])->assertRedirect('/secure-admin');
        $this->delete("/secure-admin/leads/{$lead->id}")->assertRedirect('/secure-admin');

        $this->assertSame('new', $lead->fresh()->status);
    }

    public function test_leads_page_lists_leads_with_counts(): void
    {
        $this->lead();
        $this->lead(['name' => 'Arjun', 'phone' => '9000000001', 'status' => 'booked']);

        $this->actingAs($this->admin, 'admin')
            ->get('/secure-admin/leads')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Leads/Index', false)
                ->has('leads.data', 2)
                ->where('counts.all', 2)
                ->where('counts.new', 1)
                ->where('counts.booked', 1)
                ->where('newLeadsCount', 1));
    }

    public function test_leads_can_be_filtered_and_searched(): void
    {
        $this->lead();
        $this->lead(['name' => 'Arjun Kapoor', 'phone' => '9000000001', 'concern' => 'ADHD', 'status' => 'contacted']);

        $this->actingAs($this->admin, 'admin')
            ->get('/secure-admin/leads?status=contacted')
            ->assertInertia(fn (Assert $page) => $page->has('leads.data', 1)->where('leads.data.0.name', 'Arjun Kapoor'));

        $this->actingAs($this->admin, 'admin')
            ->get('/secure-admin/leads?concern=ADHD')
            ->assertInertia(fn (Assert $page) => $page->has('leads.data', 1)->where('filters.concern', 'ADHD'));

        $this->actingAs($this->admin, 'admin')
            ->get('/secure-admin/leads?search=9000000001')
            ->assertInertia(fn (Assert $page) => $page->has('leads.data', 1)->where('leads.data.0.phone', '9000000001'));

        // Unknown filter values are ignored rather than breaking the page.
        $this->actingAs($this->admin, 'admin')
            ->get('/secure-admin/leads?status=bogus')
            ->assertInertia(fn (Assert $page) => $page->has('leads.data', 2)->where('filters.status', ''));
    }

    public function test_admin_can_change_status_and_save_notes(): void
    {
        $lead = $this->lead();

        $this->actingAs($this->admin, 'admin')
            ->patch("/secure-admin/leads/{$lead->id}", ['status' => 'contacted'])
            ->assertSessionHasNoErrors();

        $this->actingAs($this->admin, 'admin')
            ->patch("/secure-admin/leads/{$lead->id}", ['notes' => 'Called, booked Thursday.'])
            ->assertSessionHasNoErrors();

        $lead->refresh();
        $this->assertSame('contacted', $lead->status);
        $this->assertSame('Called, booked Thursday.', $lead->notes);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $lead = $this->lead();

        $this->actingAs($this->admin, 'admin')
            ->patch("/secure-admin/leads/{$lead->id}", ['status' => 'archived'])
            ->assertSessionHasErrors('status');

        $this->assertSame('new', $lead->fresh()->status);
    }

    public function test_admin_can_delete_a_lead(): void
    {
        $lead = $this->lead();

        $this->actingAs($this->admin, 'admin')
            ->delete("/secure-admin/leads/{$lead->id}")
            ->assertSessionHas('success');

        $this->assertModelMissing($lead);
    }

    public function test_dashboard_shows_lead_stats_and_latest_leads(): void
    {
        $this->lead();
        $this->lead(['phone' => '9000000001', 'status' => 'closed']);

        $this->actingAs($this->admin, 'admin')
            ->get('/secure-admin/dashboard')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Index', false)
                ->where('leadStats.new', 1)
                ->where('leadStats.total', 2)
                ->has('latestLeads', 2));
    }
}
