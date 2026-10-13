<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeSectionRequest;
use App\Support\HomeContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin → Home page: edit the homepage section by section.
 */
class HomePageController extends Controller
{
    public function edit(Request $request): Response
    {
        $section = array_key_exists($request->query('section'), HomeContent::SECTIONS) ? $request->query('section') : 'hero';

        return Inertia::render('HomeEditor/Index', [
            'sections' => HomeContent::SECTIONS,
            'content' => HomeContent::all(),
            'current' => $section,
        ]);
    }

    public function update(UpdateHomeSectionRequest $request, string $section): RedirectResponse
    {
        abort_unless(array_key_exists($section, HomeContent::SECTIONS), 404);

        $old = HomeContent::get($section);
        $data = $request->validated();

        // Uploads: store and point the image field at the new file.
        foreach (HomeContent::IMAGES[$section] ?? [] as $pattern) {
            foreach ($this->expand($pattern, $data) as $path) {
                if ($file = $request->file("{$path}_file")) {
                    Arr::set($data, $path, '/storage/'.$file->store('home', 'public'));
                }
            }
        }

        if ($section === 'seo' && ! $request->hasFile('og_image_file') && $request->boolean('og_image_remove')) {
            $data['og_image'] = '';
        }

        if ($section === 'videos') {
            foreach ($data['reels'] ?? [] as $i => $reel) {
                if ($reel['platform'] === 'youtube') {
                    $data['reels'][$i]['video'] = HomeContent::youtubeId($reel['video']);
                }
            }
        }

        $data = $this->withoutUploadKeys($data);
        HomeContent::put($section, $data);

        // Delete uploaded images that are no longer used.
        collect(HomeContent::uploadedFiles($old))
            ->diff(HomeContent::uploadedFiles($data))
            ->each(fn ($url) => Storage::disk('public')->delete(substr($url, strlen('/storage/'))));

        return redirect()
            ->route('admin.home.edit', ['section' => $section])
            ->with('success', HomeContent::SECTIONS[$section].' saved.');
    }

    /**
     * "slides.*.image" → ["slides.0.image", "slides.1.image", …]
     *
     * @return list<string>
     */
    private function expand(string $pattern, array $data): array
    {
        if (! str_contains($pattern, '.*.')) {
            return [$pattern];
        }

        [$list, $field] = explode('.*.', $pattern, 2);

        return collect(array_keys((array) Arr::get($data, $list, [])))
            ->map(fn ($i) => "{$list}.{$i}.{$field}")
            ->all();
    }

    /**
     * Drop "*_file" and "*_remove" keys before saving.
     */
    private function withoutUploadKeys(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            if (is_string($key) && (str_ends_with($key, '_file') || str_ends_with($key, '_remove'))) {
                continue;
            }
            $clean[$key] = is_array($value) ? $this->withoutUploadKeys($value) : $value;
        }

        return $clean;
    }
}
