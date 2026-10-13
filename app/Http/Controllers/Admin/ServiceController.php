<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Services: homepage cards + detail pages + SEO.
 */
class ServiceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Services/Index', [
            'services' => Service::ordered()->get()->map(fn (Service $service) => [
                'id' => $service->id,
                'title' => $service->title,
                'slug' => $service->slug,
                'icon' => $service->icon,
                'audience' => $service->audience,
                'is_published' => $service->is_published,
                'show_on_home' => $service->show_on_home,
                'is_featured' => $service->is_featured,
                'robots_index' => $service->robots_index,
                'updated_at' => $service->updated_at?->format('d M Y'),
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ServiceEditor/Index', [
            'service' => null,
            'icons' => Service::ICONS,
            'siteUrl' => url('/'),
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $service = new Service($request->serviceData());
        $this->syncImages($service, $request);
        $service->save();

        return redirect()->route('admin.services.edit', $service)->with('success', 'Service created.');
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('ServiceEditor/Index', [
            'service' => [
                ...$service->only($service->getFillable()),
                'id' => $service->id,
                'issues' => $service->issues ?? [],
                'approach' => $service->approach ?? [],
                'faqs' => $service->faqs ?? [],
                'hero_image_url' => Service::imageUrl($service->hero_image),
                'og_image_url' => Service::imageUrl($service->og_image),
                'url' => $service->url(),
                'old_slugs' => $service->redirects()->latest()->pluck('old_slug'),
                'updated_at' => $service->updated_at?->format('d M Y, h:i A'),
            ],
            'icons' => Service::ICONS,
            'siteUrl' => url('/'),
        ]);
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $service->fill($request->serviceData());
        $this->syncImages($service, $request);
        $service->save();

        return back()->with('success', 'Service saved.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', "“{$service->title}” deleted.");
    }

    /**
     * Move a service one place up or down.
     */
    public function move(Request $request, Service $service): RedirectResponse
    {
        $request->validate(['direction' => ['required', Rule::in(['up', 'down'])]]);

        DB::transaction(function () use ($request, $service) {
            // Normalise to 1..n first so swaps are always clean.
            $ids = Service::ordered()->pluck('id')->values();
            $index = $ids->search($service->id);
            $target = $request->input('direction') === 'up' ? $index - 1 : $index + 1;

            if ($target < 0 || $target >= $ids->count()) {
                return;
            }

            [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];

            $ids->each(fn ($id, $position) => Service::whereKey($id)->update(['sort_order' => $position + 1]));
        });

        return back();
    }

    /**
     * Quick switch from the list: published / on homepage.
     */
    public function toggle(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'field' => ['required', Rule::in(['is_published', 'show_on_home'])],
            'value' => ['required', 'boolean'],
        ]);

        $service->update([$data['field'] => (bool) $data['value']]);

        return back()->with('success', 'Service updated.');
    }

    /**
     * Store new uploads / remove images, deleting replaced files.
     */
    private function syncImages(Service $service, ServiceRequest $request): void
    {
        foreach (['hero_image', 'og_image'] as $field) {
            $old = $service->getOriginal($field);

            if ($request->hasFile("{$field}_file")) {
                $service->{$field} = $request->file("{$field}_file")->store('services', 'public');
            } elseif ($request->boolean("remove_{$field}")) {
                $service->{$field} = null;
            }

            if ($old && $old !== $service->{$field}) {
                Storage::disk('public')->delete($old);
            }
        }
    }
}
