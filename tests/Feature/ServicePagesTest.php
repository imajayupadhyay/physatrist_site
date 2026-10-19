<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Service;
use App\Models\Setting;
use Database\Seeders\ContentSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ServicePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed([ServiceSeeder::class, ContentSeeder::class]);
    }

    public function test_every_seeded_service_has_a_page(): void
    {
        $this->assertSame(8, Service::count());

        Service::all()->each(fn (Service $service) => $this->get("/services/{$service->slug}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('ServiceDetail/Index', false)
                ->where('service.title', $service->title)
                ->where('service.concern', $service->concernName())
                ->has('otherServices', 3)
                ->has('appointment.concerns')));
    }

    public function test_seo_tags_are_rendered_server_side(): void
    {
        $service = Service::where('slug', 'ocd-therapy')->first();

        $html = $this->get('/services/ocd-therapy')->assertOk()->getContent();

        $this->assertStringContainsString('<title inertia>'.e($service->seo_title).'</title>', $html);
        $this->assertStringContainsString('name="description" content="'.e($service->meta_description).'"', $html);
        $this->assertStringContainsString('name="keywords"', $html);
        $this->assertStringContainsString('name="robots" content="index, follow"', $html);
        $this->assertStringContainsString('rel="canonical" href="'.url('/services/ocd-therapy').'"', $html);
        $this->assertStringContainsString('property="og:title"', $html);
        $this->assertStringContainsString('property="og:image" content="http', $html);
        $this->assertStringContainsString('"@type":"FAQPage"', $html);
        $this->assertStringContainsString('"@type":"BreadcrumbList"', $html);
    }

    public function test_seo_falls_back_and_respects_custom_values(): void
    {
        Service::where('slug', 'adhd')->update([
            'seo_title' => null,
            'meta_description' => null,
            'canonical_url' => 'https://example.com/adhd',
            'robots_index' => false,
            'robots_follow' => false,
        ]);

        $this->get('/services/adhd')->assertInertia(fn (Assert $page) => $page
            ->where('seo.title', 'ADHD · Mind Essence Wellness Centre')
            ->where('seo.description', fn ($description) => str_starts_with($description, 'Difficulty focusing'))
            ->where('seo.canonical', 'https://example.com/adhd')
            ->where('seo.robots', 'noindex, nofollow'));
    }

    public function test_unknown_slug_is_404(): void
    {
        $this->get('/services/does-not-exist')->assertNotFound();
    }

    public function test_old_slug_redirects_permanently_after_slug_change(): void
    {
        $service = Service::where('slug', 'adhd')->first();
        $service->update(['slug' => 'adhd-assessment-therapy']);
        $service->update(['slug' => 'adhd-support']);

        $this->get('/services/adhd')->assertStatus(301)->assertRedirect('/services/adhd-support');
        $this->get('/services/adhd-assessment-therapy')->assertStatus(301)->assertRedirect('/services/adhd-support');
        $this->get('/services/adhd-support')->assertOk();
    }

    public function test_reusing_an_old_slug_for_a_new_page_removes_the_redirect(): void
    {
        $service = Service::where('slug', 'adhd')->first();
        $service->update(['slug' => 'adhd-support']);

        // Changing back: the slug is live again, not a redirect loop.
        $service->update(['slug' => 'adhd']);

        $this->get('/services/adhd')->assertOk();
        $this->get('/services/adhd-support')->assertStatus(301)->assertRedirect('/services/adhd');
    }

    public function test_drafts_are_hidden_from_visitors_but_previewable_by_admins(): void
    {
        Service::where('slug', 'ocd-therapy')->update(['is_published' => false]);

        $this->get('/services/ocd-therapy')->assertNotFound();

        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@gmail.com', 'password' => 'admin@123']);
        $this->actingAs($admin, 'admin')
            ->get('/services/ocd-therapy')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('isPreview', true)->where('seo.robots', 'noindex, nofollow'));
    }

    public function test_homepage_cards_and_section_are_dynamic(): void
    {
        Service::where('slug', 'adhd')->update(['show_on_home' => false]);
        Service::where('slug', 'autism-spectrum')->update(['is_published' => false]);
        Setting::put('home_services', [...config('content.home_services'), 'heading' => 'Our services']);

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index', false)
            ->has('services', 6)
            ->where('services.0.slug', 'anxiety-stress-depression')
            ->where('services.5.is_featured', true)
            ->where('servicesSection.heading', 'Our services')
            ->has('footer.columns.1.links', 6) // footer "Services" column: first 6 published
            ->where('seo.canonical', url('/')));
    }

    public function test_sitemap_lists_only_published_indexable_services(): void
    {
        Service::where('slug', 'adhd')->update(['robots_index' => false]);
        Service::where('slug', 'ocd-therapy')->update(['is_published' => false]);

        $xml = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml')->getContent();

        $this->assertMatchesRegularExpression('#<loc>'.preg_quote(url('/'), '#').'</loc>\s*<lastmod>#', $xml);
        $this->assertStringContainsString('<loc>'.url('/services/anxiety-stress-depression').'</loc>', $xml);
        $this->assertStringNotContainsString('/services/adhd<', $xml);
        $this->assertStringNotContainsString('/services/ocd-therapy<', $xml);
    }
}
