<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Support\HomeContent;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomePageManagementTest extends TestCase
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

    private function save(string $section, array $data)
    {
        return $this->actingAs($this->admin, 'admin')->put("/secure-admin/home/{$section}", $data);
    }

    /**
     * @return array<string, mixed>
     */
    private function section(string $section, array $overrides = []): array
    {
        return array_replace(config("home.{$section}"), $overrides);
    }

    public function test_homepage_renders_the_default_content(): void
    {
        $this->get('/')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Home/Index', false)
            ->has('home.hero.slides', 3)
            ->where('home.hero.slides.0.title', 'A healthier mind,')
            ->where('home.trust.items.0.value', '6+')
            ->where('home.about.heading', 'Ms. Lovleena Sharma')
            ->has('home.assessment.types', 6)
            ->has('home.steps.items', 3)
            ->has('home.space.photos', 4)
            ->where('home.videos.reels.0.video', 'kSVMb10Rj6I')
            ->has('home.testimonials.items', 4)
            ->has('home.faq.items', 11)
            ->where('seo.title', config('home.seo.title')));
    }

    public function test_guests_cannot_edit_the_homepage(): void
    {
        $this->get('/secure-admin/home')->assertRedirect('/secure-admin');
        $this->put('/secure-admin/home/trust', $this->section('trust'))->assertRedirect('/secure-admin');
    }

    public function test_editor_loads_every_section(): void
    {
        $this->actingAs($this->admin, 'admin')->get('/secure-admin/home?section=videos')
            ->assertInertia(fn (Assert $page) => $page
                ->component('HomeEditor/Index', false)
                ->where('current', 'videos')
                ->has('sections', 10)
                ->has('content.hero.slides', 3));

        $this->actingAs($this->admin, 'admin')->get('/secure-admin/home?section=nope')
            ->assertInertia(fn (Assert $page) => $page->where('current', 'hero'));

        $this->actingAs($this->admin, 'admin')->put('/secure-admin/home/nope', [])->assertNotFound();
    }

    public function test_sections_save_independently_and_show_on_the_homepage(): void
    {
        $this->save('trust', ['visible' => true, 'items' => [['value' => '10+', 'label' => 'Years'], ['value' => '', 'label' => '']]])
            ->assertRedirect('/secure-admin/home?section=trust')
            ->assertSessionHas('success', 'Trust strip saved.');

        $this->save('steps', $this->section('steps', ['heading' => 'Three easy steps']))->assertSessionHasNoErrors();

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->has('home.trust.items', 1) // empty row dropped
            ->where('home.trust.items.0.value', '10+')
            ->where('home.steps.heading', 'Three easy steps')
            ->where('home.about.heading', 'Ms. Lovleena Sharma')); // untouched section keeps defaults
    }

    public function test_sections_can_be_hidden(): void
    {
        $this->save('testimonials', $this->section('testimonials', ['visible' => false]))->assertSessionHasNoErrors();

        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('home.testimonials.visible', false));
    }

    public function test_hero_slides_validation_and_nested_card(): void
    {
        $hero = $this->section('hero');
        $hero['slides'][0]['title'] = '';
        $hero['slides'][1]['primary_url'] = 'javascript:alert(1)';
        $hero['slides'][2]['image'] = 'https://evil.example/x.jpg';
        $hero['card']['name'] = '';

        $this->save('hero', $hero)->assertSessionHasErrors(['slides.0.title', 'slides.1.primary_url', 'slides.2.image', 'card.name']);

        $this->save('hero', [...$this->section('hero'), 'slides' => []])->assertSessionHasErrors('slides');

        $hero = $this->section('hero');
        $hero['card']['visible'] = false;
        $hero['interval'] = 9;
        $this->save('hero', $hero)->assertSessionHasNoErrors();
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('home.hero.card.visible', false)->where('home.hero.interval', 9));
    }

    public function test_image_uploads_replace_and_clean_up(): void
    {
        Storage::fake('public');

        $hero = $this->section('hero');
        $hero['slides'][1]['image_file'] = UploadedFile::fake()->image('slide.jpg', 1920, 1080);
        $hero['card']['photo_file'] = UploadedFile::fake()->image('card.jpg', 400, 400);

        $this->actingAs($this->admin, 'admin')
            ->post('/secure-admin/home/hero', [...$hero, '_method' => 'put'])
            ->assertSessionHasNoErrors();

        $saved = HomeContent::get('hero');
        $slideImage = $saved['slides'][1]['image'];
        $this->assertStringStartsWith('/storage/home/', $slideImage);
        $this->assertStringStartsWith('/storage/home/', $saved['card']['photo']);
        $this->assertSame('/images/clinic-entrance.jpg', $saved['slides'][0]['image']); // untouched
        Storage::disk('public')->assertExists(substr($slideImage, 9));
        $this->assertArrayNotHasKey('image_file', $saved['slides'][1]);

        // Replace the slide image → the old upload is deleted.
        $saved['slides'][1]['image_file'] = UploadedFile::fake()->image('slide2.jpg');
        $this->actingAs($this->admin, 'admin')->post('/secure-admin/home/hero', [...$saved, '_method' => 'put'])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing(substr($slideImage, 9));

        // Removing a slide that used an upload deletes the file.
        $current = HomeContent::get('hero');
        $upload = $current['slides'][1]['image'];
        array_splice($current['slides'], 1, 1);
        $this->save('hero', $current)->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing(substr($upload, 9));

        // Non-images are rejected.
        $bad = $this->section('about');
        $bad['image_file'] = UploadedFile::fake()->create('evil.php', 5, 'application/x-php');
        $this->actingAs($this->admin, 'admin')->post('/secure-admin/home/about', [...$bad, '_method' => 'put'])->assertSessionHasErrors('image_file');
    }

    public function test_space_needs_exactly_four_photos(): void
    {
        $space = $this->section('space');
        array_pop($space['photos']);

        $this->save('space', $space)->assertSessionHasErrors('photos');
    }

    public function test_video_links_are_validated_and_normalised(): void
    {
        $videos = $this->section('videos');
        $videos['reels'][0]['video'] = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10s';
        $videos['reels'][1]['video'] = 'https://youtu.be/dQw4w9WgXcQ';
        $videos['reels'][1]['platform'] = 'youtube';
        $this->save('videos', $videos)->assertSessionHasNoErrors();

        $saved = HomeContent::get('videos');
        $this->assertSame('dQw4w9WgXcQ', $saved['reels'][0]['video']);
        $this->assertSame('dQw4w9WgXcQ', $saved['reels'][1]['video']);

        $videos['reels'][0]['video'] = 'https://vimeo.com/123';
        $videos['reels'][2]['video'] = 'https://example.com/reel';
        $this->save('videos', $videos)->assertSessionHasErrors(['reels.0.video', 'reels.2.video']);
    }

    public function test_testimonial_ratings_and_faq_links(): void
    {
        $this->save('testimonials', $this->section('testimonials', ['items' => [['quote' => 'Great', 'name' => 'J.', 'service' => '', 'rating' => 6]]]))
            ->assertSessionHasErrors('items.0.rating');

        $this->save('faq', $this->section('faq', ['items' => [['question' => 'Where?', 'answer' => 'See [our map](https://maps.example.com).']]]))
            ->assertSessionHasNoErrors();
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('home.faq.items.0.answer', 'See [our map](https://maps.example.com).'));
    }

    public function test_homepage_seo_is_editable_and_rendered_server_side(): void
    {
        $this->save('seo', [
            'title' => 'Clinical Psychologist in New Delhi | Mind Essence',
            'description' => 'Therapy and assessment in New Delhi.',
            'keywords' => 'psychologist delhi',
            'og_title' => '',
            'og_description' => '',
            'og_image' => '',
        ])->assertSessionHasNoErrors();

        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('<title inertia>Clinical Psychologist in New Delhi | Mind Essence</title>', $html);
        $this->assertStringContainsString('name="description" content="Therapy and assessment in New Delhi."', $html);
        $this->assertStringContainsString('name="keywords" content="psychologist delhi"', $html);

        $this->save('seo', ['title' => '', 'description' => ''])->assertSessionHasErrors(['title', 'description']);
    }
}
