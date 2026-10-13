<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Service;
use App\Models\Setting;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FooterManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(ServiceSeeder::class);
        $this->admin = Admin::create(['name' => 'Admin', 'email' => 'admin@gmail.com', 'password' => 'admin@123']);
    }

    /**
     * The form as the admin page sends it (defaults), with overrides.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_replace_recursive([
            ...config('content.footer'),
            'contact' => config('content.contact'),
        ], $overrides);
    }

    private function save(array $payload)
    {
        return $this->actingAs($this->admin, 'admin')->put('/secure-admin/footer', $payload);
    }

    public function test_website_gets_the_default_footer_and_contact_details(): void
    {
        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('footer.columns.0.heading', 'Explore')
            ->where('footer.columns.0.links.0.href', '/#about')
            ->where('footer.columns.1.heading', 'Services')
            ->where('footer.columns.1.links.0.href', '/services/anxiety-stress-depression')
            ->has('footer.columns.1.links', 6)
            ->where('footer.credentials.name', 'Ms. Loveleena Sharma')
            ->has('footer.credentials.lines', 2)
            ->where('footer.copyright', '© '.now()->year.' Mind Essence Wellness Centre. All rights reserved.')
            ->where('contact.phones.0', ['display' => '+91 82853 54608', 'tel' => '+918285354608'])
            ->where('contact.whatsappNumber', '918285354608')
            ->where('contact.email', 'lovleenasharma49@gmail.com'));
    }

    public function test_footer_and_contact_are_shared_on_service_pages_too(): void
    {
        $this->get('/services/adhd')->assertInertia(fn (Assert $page) => $page->has('footer.columns')->has('contact.phones'));
    }

    public function test_guests_cannot_edit_the_footer(): void
    {
        $this->get('/secure-admin/footer')->assertRedirect('/secure-admin');
        $this->put('/secure-admin/footer', $this->payload())->assertRedirect('/secure-admin');
    }

    public function test_footer_admin_page_loads(): void
    {
        $this->actingAs($this->admin, 'admin')->get('/secure-admin/footer')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Footer/Index', false)
                ->has('footer.columns', 2)
                ->where('contact.email', 'lovleenasharma49@gmail.com')
                ->where('publishedServices', 8));
    }

    public function test_admin_can_update_columns_and_texts(): void
    {
        $this->save($this->payload([
            'columns' => [
                ['heading' => 'Services', 'type' => 'services', 'limit' => 3, 'links' => [['label' => 'ignored', 'url' => '/x']]],
                ['heading' => 'Help', 'type' => 'links', 'links' => [
                    ['label' => 'FAQ', 'url' => '/#faq', 'new_tab' => false],
                    ['label' => '', 'url' => '', 'new_tab' => false], // empty row dropped
                    ['label' => 'Tele-MANAS', 'url' => 'https://telemanas.mohfw.gov.in', 'new_tab' => true],
                ]],
                ['heading' => 'Legal', 'type' => 'links', 'links' => [['label' => 'Privacy', 'url' => '/privacy', 'new_tab' => false]]],
            ],
            'copyright_text' => '© {year} Mind Essence',
            'helpline_visible' => false,
            'credentials_name' => '',
            'credentials_text' => '',
        ]))->assertSessionHasNoErrors();

        $saved = Setting::get('footer');
        $this->assertSame([], $saved['columns'][0]['links']);
        $this->assertCount(2, $saved['columns'][1]['links']);

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->has('footer.columns', 3)
            ->has('footer.columns.0.links', 3)
            ->where('footer.columns.1.links.1.newTab', true)
            ->where('footer.copyright', '© '.now()->year.' Mind Essence')
            ->where('footer.helpline', null)
            ->where('footer.credentials', null));
    }

    public function test_services_column_follows_published_services_and_order(): void
    {
        Service::where('slug', 'anxiety-stress-depression')->update(['is_published' => false]);

        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('footer.columns.1.links.0.label', 'OCD Therapy'));
    }

    public function test_contact_details_are_updated_site_wide(): void
    {
        $this->save($this->payload(['contact' => [
            'email' => 'hello@mindessence.in',
            'phones' => ['+91 98100 00000', ''],
            'whatsapp_number' => '+91 98100-00000',
            'facebook' => 'https://www.facebook.com/mindessence',
        ]]))->assertSessionHasNoErrors();

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('contact.email', 'hello@mindessence.in')
            ->has('contact.phones', 1)
            ->where('contact.phones.0.tel', '+919810000000')
            ->where('contact.whatsappNumber', '919810000000')
            ->where('contact.facebook', 'https://www.facebook.com/mindessence'));

        // Also in the server-rendered structured data.
        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('"telephone":["+919810000000"]', $html);
        $this->assertStringContainsString('"email":"hello@mindessence.in"', $html);
    }

    public function test_validation(): void
    {
        $this->save($this->payload([
            'brand_name' => '',
            'contact_heading' => '',
            'helpline_visible' => true,
            'helpline_text' => '',
            'columns' => [
                ['heading' => '', 'type' => 'links', 'links' => [['label' => 'Bad', 'url' => 'javascript:alert(1)']]],
                ['heading' => 'A', 'type' => 'links', 'links' => []],
                ['heading' => 'B', 'type' => 'links', 'links' => []],
                ['heading' => 'C', 'type' => 'links', 'links' => []],
            ],
            'contact' => [
                'email' => 'nope',
                'phones' => ['abc'],
                'whatsapp_number' => '123',
                'address' => '',
                'instagram' => 'instagram.com/me',
            ],
        ]))->assertSessionHasErrors([
            'brand_name', 'contact_heading', 'helpline_text', 'columns',
            'contact.email', 'contact.phones.0', 'contact.whatsapp_number', 'contact.address', 'contact.instagram',
        ]);

        $payload = $this->payload(['columns' => [['heading' => '', 'type' => 'links', 'links' => [['label' => 'Bad', 'url' => 'javascript:alert(1)']]]]]);
        $payload['contact']['phones'] = []; // array_replace_recursive can't empty a list
        $this->save($payload)->assertSessionHasErrors(['columns.0.heading', 'columns.0.links.0.url', 'contact.phones']);
    }

    public function test_footer_logo_upload_and_remove(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin, 'admin')->post('/secure-admin/footer', [
            ...$this->payload(),
            '_method' => 'put',
            'logo_file' => UploadedFile::fake()->image('footer-logo.png', 400, 120),
        ])->assertSessionHasNoErrors();

        $path = Setting::get('footer')['logo'];
        Storage::disk('public')->assertExists($path);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('footer.logo.url', fn ($url) => str_contains($url, $path)));

        $this->save($this->payload(['remove_logo' => true]))->assertSessionHasNoErrors();
        $this->assertNull(Setting::get('footer')['logo']);
        Storage::disk('public')->assertMissing($path);
    }
}
