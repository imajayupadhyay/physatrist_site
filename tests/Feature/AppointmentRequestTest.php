<?php

namespace Tests\Feature;

use App\Models\Lead;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AppointmentRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(ServiceSeeder::class); // concern options come from services
        RateLimiter::clear('leads:127.0.0.1');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => '  Riya Mehta ',
            'phone' => '+91 98765 43210',
            'email' => 'riya@example.com',
            'concern' => 'OCD Therapy',
            'preferred_date' => now()->addDays(3)->toDateString(),
            'mode' => 'Online',
            'message' => 'Evenings are better for me.',
            'website' => '',
            ...$overrides,
        ];
    }

    public function test_home_page_receives_form_options(): void
    {
        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index', false)
            ->where('appointment.concerns', Lead::concernOptions())
            ->where('appointment.concerns.0', 'Not sure yet')
            ->where('appointment.concerns.2', 'OCD Therapy')
            ->where('appointment.modes', config('leads.modes')));
    }

    public function test_valid_submission_is_stored_as_a_new_lead(): void
    {
        $this->from('/')
            ->post('/appointment-requests', $this->payload())
            ->assertRedirect('/')
            ->assertSessionHasNoErrors();

        $lead = Lead::sole();
        $this->assertSame('Riya Mehta', $lead->name); // trimmed
        $this->assertSame('+91 98765 43210', $lead->phone);
        $this->assertSame('OCD Therapy', $lead->concern);
        $this->assertSame('Online', $lead->mode);
        $this->assertSame('new', $lead->status);
        $this->assertSame('homepage_appointment', $lead->source);
        $this->assertSame(now()->addDays(3)->toDateString(), $lead->preferred_date->toDateString());
        $this->assertNotNull($lead->ip_address);
    }

    public function test_optional_fields_can_be_left_empty(): void
    {
        $this->post('/appointment-requests', $this->payload(['email' => '', 'preferred_date' => '', 'message' => '']))
            ->assertSessionHasNoErrors();

        $lead = Lead::sole();
        $this->assertNull($lead->email);
        $this->assertNull($lead->preferred_date);
        $this->assertNull($lead->message);
    }

    public function test_required_fields_and_formats_are_validated(): void
    {
        $this->post('/appointment-requests', [
            'name' => '',
            'phone' => '12345',
            'email' => 'not-an-email',
            'concern' => 'Something made up',
            'preferred_date' => now()->subDay()->toDateString(),
            'mode' => 'Telepathy',
        ])->assertSessionHasErrors(['name', 'phone', 'email', 'concern', 'preferred_date', 'mode']);

        $this->assertSame(0, Lead::count());
    }

    public function test_phone_must_contain_10_to_15_digits(): void
    {
        $this->post('/appointment-requests', $this->payload(['phone' => 'abc-defg-hijk']))->assertSessionHasErrors('phone');
        $this->post('/appointment-requests', $this->payload(['phone' => '98765 4321']))->assertSessionHasErrors('phone'); // 9 digits
        $this->assertSame(0, Lead::count());
    }

    public function test_concern_must_be_a_published_service(): void
    {
        \App\Models\Service::where('slug', 'ocd-therapy')->update(['is_published' => false]);

        $this->post('/appointment-requests', $this->payload(['concern' => 'OCD Therapy']))->assertSessionHasErrors('concern');
        $this->post('/appointment-requests', $this->payload(['concern' => 'Other']))->assertSessionHasNoErrors();
    }

    public function test_honeypot_submissions_are_silently_dropped(): void
    {
        $this->from('/')
            ->post('/appointment-requests', $this->payload(['website' => 'http://spam.example']))
            ->assertRedirect('/')
            ->assertSessionHasNoErrors();

        $this->assertSame(0, Lead::count());
    }

    public function test_double_submit_does_not_create_a_duplicate(): void
    {
        $this->post('/appointment-requests', $this->payload());
        $this->post('/appointment-requests', $this->payload())->assertSessionHasNoErrors();

        $this->assertSame(1, Lead::count());
    }

    public function test_submissions_are_rate_limited_per_ip(): void
    {
        $limit = config('leads.rate_limit_per_hour');

        for ($i = 0; $i < $limit; $i++) {
            $this->post('/appointment-requests', $this->payload(['phone' => '98765432'.str_pad((string) $i, 2, '0', STR_PAD_LEFT)]))
                ->assertSessionHasNoErrors();
        }

        $this->post('/appointment-requests', $this->payload(['phone' => '9123456789']))
            ->assertSessionHasErrors('form');

        $this->assertSame($limit, Lead::count());
    }
}
