<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Service;
use App\Models\Setting;
use Database\Seeders\ContentSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed([ServiceSeeder::class, ContentSeeder::class]);
        $this->admin = Admin::create(['name' => 'Admin', 'email' => 'admin@gmail.com', 'password' => 'admin@123']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'title' => 'Grief Counselling',
            'slug' => '',
            'icon' => 'leaf',
            'summary' => 'Support through loss.',
            'hero_title' => 'Gentle support through grief',
            'intro' => 'Grief can feel overwhelming.',
            'is_published' => true,
            'show_on_home' => true,
            'issues' => ['Loss of a loved one', '', '  '],
            'approach' => [['title' => 'Listen', 'text' => 'We start by listening.'], ['title' => '', 'text' => '']],
            'faqs' => [['question' => 'How long?', 'answer' => 'It varies.']],
            'robots_index' => true,
            'robots_follow' => true,
            ...$overrides,
        ];
    }

    public function test_guests_cannot_manage_services(): void
    {
        $this->get('/secure-admin/services')->assertRedirect('/secure-admin');
        $this->post('/secure-admin/services', $this->payload())->assertRedirect('/secure-admin');
        $this->assertSame(8, Service::count());
    }

    public function test_services_list_and_editor_load(): void
    {
        $this->actingAs($this->admin, 'admin')->get('/secure-admin/services')
            ->assertInertia(fn (Assert $page) => $page->component('Services/Index', false)->has('services', 8));

        $service = Service::first();
        $this->actingAs($this->admin, 'admin')->get("/secure-admin/services/{$service->id}/edit")
            ->assertInertia(fn (Assert $page) => $page->component('ServiceEditor/Index', false)->where('service.slug', $service->slug));

        $this->actingAs($this->admin, 'admin')->get('/secure-admin/services/create')
            ->assertInertia(fn (Assert $page) => $page->component('ServiceEditor/Index', false)->where('service', null));
    }

    public function test_admin_can_create_a_service_with_auto_slug_and_cleaned_lists(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/secure-admin/services', $this->payload())
            ->assertSessionHasNoErrors();

        $service = Service::where('slug', 'grief-counselling')->firstOrFail();
        $this->assertSame(['Loss of a loved one'], $service->issues);
        $this->assertCount(1, $service->approach);
        $this->assertSame(9, $service->sort_order); // appended to the end
        $this->get('/services/grief-counselling')->assertOk();
        $this->assertContains('Grief Counselling', \App\Models\Lead::concernOptions());
    }

    public function test_slug_must_be_unique_and_well_formed(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/secure-admin/services', $this->payload(['slug' => 'ocd-therapy']))
            ->assertSessionHasErrors('slug');

        // Messy input is normalised rather than rejected.
        $this->actingAs($this->admin, 'admin')
            ->post('/secure-admin/services', $this->payload(['slug' => '  Grief & Loss SUPPORT!! ']))
            ->assertSessionHasNoErrors();
        $this->assertTrue(Service::where('slug', 'grief-loss-support')->exists());
    }

    public function test_required_fields_and_nested_rows_are_validated(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/secure-admin/services', $this->payload([
                'title' => '',
                'summary' => '',
                'icon' => 'rocket',
                'hero_title' => '',
                'intro' => '',
                'canonical_url' => 'not-a-url',
                'faqs' => [['question' => 'Only a question', 'answer' => '']],
            ]))
            ->assertSessionHasErrors(['title', 'summary', 'icon', 'hero_title', 'intro', 'canonical_url', 'faqs.0.answer']);
    }

    public function test_admin_can_update_content_and_seo_and_slug_change_creates_redirect(): void
    {
        $service = Service::where('slug', 'ocd-therapy')->first();

        $this->actingAs($this->admin, 'admin')
            ->put("/secure-admin/services/{$service->id}", $this->payload([
                'title' => 'OCD Treatment',
                'slug' => 'ocd-treatment',
                'seo_title' => 'OCD Treatment in Delhi',
                'meta_description' => 'New description',
                'robots_index' => false,
            ]))
            ->assertSessionHasNoErrors();

        $service->refresh();
        $this->assertSame('OCD Treatment', $service->title);
        $this->assertSame('OCD Treatment in Delhi', $service->seo_title);
        $this->assertFalse($service->robots_index);
        $this->get('/services/ocd-therapy')->assertStatus(301)->assertRedirect('/services/ocd-treatment');
    }

    public function test_images_can_be_uploaded_replaced_and_removed(): void
    {
        Storage::fake('public');
        $service = Service::first();

        $this->actingAs($this->admin, 'admin')->post("/secure-admin/services/{$service->id}", [
            ...$this->payload(['slug' => $service->slug]),
            '_method' => 'put',
            'hero_image_file' => UploadedFile::fake()->image('hero.jpg', 800, 1000),
            'og_image_file' => UploadedFile::fake()->image('share.png', 1200, 630),
        ])->assertSessionHasNoErrors();

        $service->refresh();
        Storage::disk('public')->assertExists($service->hero_image);
        Storage::disk('public')->assertExists($service->og_image);
        $oldHero = $service->hero_image;

        $this->get("/services/{$service->slug}")->assertInertia(fn (Assert $page) => $page
            ->where('seo.og.image', fn ($url) => str_contains($url, $service->og_image)));

        // Replace hero, remove share image.
        $this->actingAs($this->admin, 'admin')->post("/secure-admin/services/{$service->id}", [
            ...$this->payload(['slug' => $service->slug]),
            '_method' => 'put',
            'hero_image_file' => UploadedFile::fake()->image('hero2.jpg'),
            'remove_og_image' => true,
        ])->assertSessionHasNoErrors();

        $service->refresh();
        Storage::disk('public')->assertMissing($oldHero);
        Storage::disk('public')->assertExists($service->hero_image);
        $this->assertNull($service->og_image);
    }

    public function test_non_images_are_rejected(): void
    {
        $service = Service::first();

        $this->actingAs($this->admin, 'admin')->post("/secure-admin/services/{$service->id}", [
            ...$this->payload(['slug' => $service->slug]),
            '_method' => 'put',
            'hero_image_file' => UploadedFile::fake()->create('virus.php', 10, 'application/x-php'),
        ])->assertSessionHasErrors('hero_image_file');
    }

    public function test_services_can_be_reordered_and_toggled(): void
    {
        $first = Service::ordered()->first();
        $second = Service::ordered()->skip(1)->first();

        $this->actingAs($this->admin, 'admin')->post("/secure-admin/services/{$second->id}/move", ['direction' => 'up']);
        $this->assertSame([$second->id, $first->id], Service::ordered()->limit(2)->pluck('id')->all());

        // Moving the first one up is a no-op.
        $this->actingAs($this->admin, 'admin')->post("/secure-admin/services/{$second->id}/move", ['direction' => 'up']);
        $this->assertSame($second->id, Service::ordered()->value('id'));

        $this->actingAs($this->admin, 'admin')->patch("/secure-admin/services/{$first->id}/toggle", ['field' => 'show_on_home', 'value' => false]);
        $this->assertFalse($first->fresh()->show_on_home);

        $this->actingAs($this->admin, 'admin')
            ->patch("/secure-admin/services/{$first->id}/toggle", ['field' => 'slug', 'value' => true])
            ->assertSessionHasErrors('field');
    }

    public function test_admin_can_delete_a_service(): void
    {
        $service = Service::where('slug', 'adhd')->first();

        $this->actingAs($this->admin, 'admin')
            ->delete("/secure-admin/services/{$service->id}")
            ->assertRedirect('/secure-admin/services');

        $this->assertModelMissing($service);
        $this->get('/services/adhd')->assertNotFound();
    }

    public function test_admin_can_edit_the_homepage_services_section(): void
    {
        $this->actingAs($this->admin, 'admin')->get('/secure-admin/services/homepage')
            ->assertInertia(fn (Assert $page) => $page->component('HomeServices/Index', false)->has('cards', 8));

        $this->actingAs($this->admin, 'admin')->put('/secure-admin/services/homepage', [
            'eyebrow' => 'What we do',
            'heading' => 'Care for every stage of life',
            'lead' => 'Short intro.',
            'card_link_label' => 'Read more',
            'also_label' => 'Also',
            'also_items' => ['Grief', '', 'Anger'],
            'notes' => [],
        ])->assertSessionHasNoErrors();

        $saved = Setting::get('home_services');
        $this->assertSame('Care for every stage of life', $saved['heading']);
        $this->assertSame(['Grief', 'Anger'], $saved['also_items']);

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('servicesSection.heading', 'Care for every stage of life')
            ->where('servicesSection.card_link_label', 'Read more'));

        $this->actingAs($this->admin, 'admin')
            ->put('/secure-admin/services/homepage', ['heading' => '', 'card_link_label' => ''])
            ->assertSessionHasErrors(['heading', 'card_link_label']);
    }
}
