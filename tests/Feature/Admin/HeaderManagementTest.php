<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\MenuItem;
use App\Models\Service;
use App\Models\Setting;
use Database\Seeders\MenuSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HeaderManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed([ServiceSeeder::class, MenuSeeder::class]);
        $this->admin = Admin::create(['name' => 'Admin', 'email' => 'admin@gmail.com', 'password' => 'admin@123']);
    }

    private function services(): MenuItem
    {
        return MenuItem::topLevel()->where('label', 'Services')->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function settingsPayload(array $overrides = []): array
    {
        return [
            ...config('content.header'),
            ...$overrides,
        ];
    }

    public function test_seeded_menu_matches_the_original_header_with_services_mega_menu(): void
    {
        $this->assertSame(
            ['About', 'Services', 'Assessments', 'Videos', 'FAQ', 'Contact'],
            MenuItem::topLevel()->ordered()->get()->map->displayLabel()->all(),
        );
        $this->assertSame('mega', $this->services()->dropdown_style);
        $this->assertSame(8, $this->services()->children()->count());

        // Seeder never duplicates.
        $this->seed(MenuSeeder::class);
        $this->assertSame(14, MenuItem::count());
    }

    public function test_website_receives_header_with_menu_tree_and_settings(): void
    {
        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->has('header.menu', 6)
            ->where('header.menu.1.label', 'Services')
            ->where('header.menu.1.style', 'mega')
            ->has('header.menu.1.children', 8)
            ->where('header.menu.1.children.1.label', 'OCD Therapy')
            ->where('header.menu.1.children.1.href', '/services/ocd-therapy')
            ->where('header.menu.1.children.1.icon', 'ocd')
            ->where('header.menu.0.style', null)
            ->where('header.cta.label', 'Book a Session')
            ->where('header.cta.href', '/#appointment')
            ->where('header.logo.url', null)
            ->has('header.megaPromo.title'));
    }

    public function test_hidden_items_and_unpublished_services_are_not_on_the_website(): void
    {
        MenuItem::where('label', 'Videos')->update(['is_visible' => false]);
        Service::where('slug', 'adhd')->update(['is_published' => false]);

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->has('header.menu', 5)
            ->has('header.menu.1.children', 7));
    }

    public function test_service_menu_links_follow_slug_changes_and_service_deletion(): void
    {
        Service::where('slug', 'ocd-therapy')->first()->update(['slug' => 'ocd-treatment']);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('header.menu.1.children.1.href', '/services/ocd-treatment'));

        Service::where('slug', 'adhd')->first()->delete();
        $this->assertSame(7, $this->services()->children()->count());
    }

    public function test_guests_cannot_manage_the_header(): void
    {
        $this->get('/secure-admin/header')->assertRedirect('/secure-admin');
        $this->post('/secure-admin/header/menu', ['type' => 'link', 'label' => 'X', 'url' => '/x', 'dropdown_style' => 'simple'])->assertRedirect('/secure-admin');
        $this->assertSame(14, MenuItem::count());
    }

    public function test_header_admin_page_loads(): void
    {
        $this->actingAs($this->admin, 'admin')->get('/secure-admin/header')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Header/Index', false)
                ->has('menu', 6)
                ->has('menu.1.children', 8)
                ->has('services', 8)
                ->where('settings.cta_label', 'Book a Session'));
    }

    public function test_admin_can_add_link_and_sub_items(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->post('/secure-admin/header/menu', ['type' => 'link', 'label' => 'Blog', 'url' => '/blog', 'dropdown_style' => 'simple'])
            ->assertSessionHasNoErrors();

        $blog = MenuItem::where('label', 'Blog')->firstOrFail();
        $this->assertNull($blog->parent_id);
        $this->assertSame(7, $blog->sort_order); // appended

        $this->actingAs($this->admin, 'admin')
            ->post('/secure-admin/header/menu', ['type' => 'link', 'label' => 'Articles', 'url' => 'https://example.com/articles', 'parent_id' => $blog->id, 'dropdown_style' => 'simple', 'open_in_new_tab' => true])
            ->assertSessionHasNoErrors();

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('header.menu.6.label', 'Blog')
            ->where('header.menu.6.style', 'simple')
            ->where('header.menu.6.children.0.href', 'https://example.com/articles')
            ->where('header.menu.6.children.0.newTab', true));
    }

    public function test_menu_item_validation(): void
    {
        $send = fn (array $data) => $this->actingAs($this->admin, 'admin')->post('/secure-admin/header/menu', [...['type' => 'link', 'dropdown_style' => 'simple'], ...$data]);

        $send(['label' => '', 'url' => ''])->assertSessionHasErrors(['label', 'url']);
        $send(['label' => 'Bad', 'url' => 'javascript:alert(1)'])->assertSessionHasErrors('url');
        $send(['label' => 'Bad', 'url' => '//evil.example'])->assertSessionHasErrors('url');
        $send(['type' => 'service', 'service_id' => ''])->assertSessionHasErrors('service_id');
        $send(['label' => 'X', 'url' => '/x', 'dropdown_style' => 'huge'])->assertSessionHasErrors('dropdown_style');

        // Only two levels: can't nest under a sub-item.
        $child = $this->services()->children()->first();
        $send(['label' => 'Deep', 'url' => '/deep', 'parent_id' => $child->id])->assertSessionHasErrors('parent_id');

        foreach (['/', '/#faq', '#contact', 'https://example.com', 'mailto:hi@example.com', 'tel:+918285354608'] as $ok) {
            $send(['label' => 'OK', 'url' => $ok])->assertSessionHasNoErrors();
        }
    }

    public function test_item_with_children_cannot_become_a_sub_item(): void
    {
        $services = $this->services();
        $about = MenuItem::where('label', 'About')->first();

        $this->actingAs($this->admin, 'admin')
            ->put("/secure-admin/header/menu/{$services->id}", ['type' => 'link', 'label' => 'Services', 'url' => '/#services', 'parent_id' => $about->id, 'dropdown_style' => 'mega'])
            ->assertSessionHasErrors('parent_id');
    }

    public function test_admin_can_edit_move_toggle_and_delete_items(): void
    {
        $about = MenuItem::where('label', 'About')->first();
        $services = $this->services();

        $this->actingAs($this->admin, 'admin')
            ->put("/secure-admin/header/menu/{$about->id}", ['type' => 'link', 'label' => 'About us', 'url' => '/#about', 'dropdown_style' => 'simple', 'is_visible' => true])
            ->assertSessionHasNoErrors();
        $this->assertSame('About us', $about->fresh()->label);

        $this->actingAs($this->admin, 'admin')->post("/secure-admin/header/menu/{$services->id}/move", ['direction' => 'up']);
        $this->assertSame(['Services', 'About us'], MenuItem::topLevel()->ordered()->limit(2)->get()->map->displayLabel()->all());

        // Reordering inside the dropdown only affects siblings.
        $second = $services->children()->skip(1)->first();
        $this->actingAs($this->admin, 'admin')->post("/secure-admin/header/menu/{$second->id}/move", ['direction' => 'up']);
        $this->assertSame($second->id, $services->children()->first()->id);

        $this->actingAs($this->admin, 'admin')->patch("/secure-admin/header/menu/{$about->id}/toggle");
        $this->assertFalse($about->fresh()->is_visible);

        $this->actingAs($this->admin, 'admin')->delete("/secure-admin/header/menu/{$services->id}")->assertSessionHas('success');
        $this->assertSame(0, MenuItem::where('parent_id', $services->id)->count()); // children removed too
        $this->assertSame(8, Service::count()); // services themselves untouched
    }

    public function test_service_items_and_bulk_add_skip_duplicates(): void
    {
        $services = $this->services();
        $services->children()->limit(3)->get()->each->delete();
        $this->assertSame(5, $services->children()->count());

        $this->actingAs($this->admin, 'admin')
            ->post("/secure-admin/header/menu/{$services->id}/services", ['service_ids' => Service::pluck('id')->all()])
            ->assertSessionHas('success', '3 service pages added.');

        $this->assertSame(8, $services->children()->count());

        // A label override wins over the service title.
        $item = $services->children()->first();
        $this->actingAs($this->admin, 'admin')
            ->put("/secure-admin/header/menu/{$item->id}", ['type' => 'service', 'service_id' => $item->service_id, 'label' => 'Short name', 'parent_id' => $services->id, 'dropdown_style' => 'simple', 'is_visible' => true])
            ->assertSessionHasNoErrors();
        $this->assertSame('Short name', $item->fresh()->displayLabel());
        $this->assertNull($item->fresh()->url);
    }

    public function test_admin_can_update_cta_and_mega_promo(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/secure-admin/header', $this->settingsPayload([
                'cta_label' => 'Book now',
                'cta_url' => '/services/ocd-therapy',
                'cta_new_tab' => true,
                'mega_promo_visible' => false,
                'brand_tagline' => '',
            ]))
            ->assertSessionHasNoErrors();

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('header.cta.label', 'Book now')
            ->where('header.cta.href', '/services/ocd-therapy')
            ->where('header.cta.newTab', true)
            ->where('header.megaPromo', null));

        $this->actingAs($this->admin, 'admin')->put('/secure-admin/header', $this->settingsPayload(['cta_visible' => false]));
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('header.cta', null));
    }

    public function test_website_receives_default_top_bar(): void
    {
        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('header.topbar', [
                'showHours' => true,
                'text' => 'In-clinic & online sessions',
                'showPhone' => true,
                'showEmail' => true,
            ])
            ->where('contact.hours', 'Mon – Sat · 10:00 AM – 7:00 PM'));
    }

    public function test_admin_can_update_the_top_bar(): void
    {
        $this->actingAs($this->admin, 'admin')->get('/secure-admin/header')
            ->assertInertia(fn (Assert $page) => $page
                ->where('settings.topbar_text', 'In-clinic & online sessions')
                ->where('contact.email', 'lovleenasharma49@gmail.com'));

        $this->actingAs($this->admin, 'admin')
            ->put('/secure-admin/header', $this->settingsPayload([
                'topbar_text' => '  Now booking weekend sessions  ',
                'topbar_show_hours' => false,
                'topbar_show_email' => false,
            ]))
            ->assertSessionHasNoErrors();

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('header.topbar', [
                'showHours' => false,
                'text' => 'Now booking weekend sessions',
                'showPhone' => true,
                'showEmail' => false,
            ]));

        // Switched off, or nothing left to show → no top bar.
        $this->actingAs($this->admin, 'admin')->put('/secure-admin/header', $this->settingsPayload(['topbar_visible' => false]));
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('header.topbar', null));

        $this->actingAs($this->admin, 'admin')->put('/secure-admin/header', $this->settingsPayload([
            'topbar_show_hours' => false,
            'topbar_text' => '',
            'topbar_show_phone' => false,
            'topbar_show_email' => false,
        ]))->assertSessionHasNoErrors();
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('header.topbar', null));

        // Other header settings are unaffected.
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('header.cta.label', 'Book a Session'));
    }

    public function test_top_bar_message_is_limited(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/secure-admin/header', $this->settingsPayload(['topbar_text' => str_repeat('a', 81)]))
            ->assertSessionHasErrors('topbar_text');
    }

    public function test_header_settings_validation(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put('/secure-admin/header', $this->settingsPayload([
                'cta_visible' => true,
                'cta_label' => '',
                'cta_url' => 'javascript:void(0)',
                'brand_name' => '',
                'logo_height' => 500,
            ]))
            ->assertSessionHasErrors(['cta_label', 'cta_url', 'brand_name', 'logo_height']);

        // Hidden CTA doesn't need its fields.
        $this->actingAs($this->admin, 'admin')
            ->put('/secure-admin/header', $this->settingsPayload(['cta_visible' => false, 'cta_label' => '', 'cta_url' => '']))
            ->assertSessionHasNoErrors();
    }

    public function test_logo_upload_replace_and_remove(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')->post('/secure-admin/header', [
            ...$this->settingsPayload(),
            '_method' => 'put',
            'logo_file' => UploadedFile::fake()->image('logo.png', 400, 120),
        ])->assertSessionHasNoErrors();

        $first = Setting::get('header')['logo'];
        Storage::disk('public')->assertExists($first);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('header.logo.url', fn ($url) => str_contains($url, $first)));

        $this->actingAs($this->admin, 'admin')->post('/secure-admin/header', [
            ...$this->settingsPayload(),
            '_method' => 'put',
            'logo_file' => UploadedFile::fake()->image('logo2.jpg'),
        ]);
        Storage::disk('public')->assertMissing($first);

        // Saving other settings keeps the logo.
        $second = Setting::get('header')['logo'];
        $this->actingAs($this->admin, 'admin')->put('/secure-admin/header', $this->settingsPayload(['cta_label' => 'Hi']));
        $this->assertSame($second, Setting::get('header')['logo']);

        $this->actingAs($this->admin, 'admin')->put('/secure-admin/header', $this->settingsPayload(['remove_logo' => true]));
        $this->assertNull(Setting::get('header')['logo']);
        Storage::disk('public')->assertMissing($second);

        $this->actingAs($this->admin, 'admin')->post('/secure-admin/header', [
            ...$this->settingsPayload(),
            '_method' => 'put',
            'logo_file' => UploadedFile::fake()->create('logo.svg', 5, 'image/svg+xml'),
        ])->assertSessionHasErrors('logo_file');
    }
}
