<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Service;
use App\Models\ServiceSlugRedirect;
use App\Support\Seo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Public service detail pages: /services/{slug}.
 */
class ServiceController extends Controller
{
    public function show(Request $request, string $slug): Response|RedirectResponse
    {
        $service = Service::where('slug', $slug)->first();

        // Old URL after a slug change → permanent redirect to the current one.
        if (! $service) {
            $redirect = ServiceSlugRedirect::with('service')->where('old_slug', $slug)->first();

            abort_unless($redirect?->service, 404);

            return redirect()->route('services.show', $redirect->service->slug, 301);
        }

        // Drafts are only visible to logged-in admins (preview).
        $isPreview = ! $service->is_published;
        abort_if($isPreview && ! $request->user('admin'), 404);

        $seo = Seo::service($service);
        if ($isPreview) {
            $seo['robots'] = 'noindex, nofollow';
        }

        return Inertia::render('ServiceDetail/Index', [
            'seo' => $seo,
            'isPreview' => $isPreview,
            'service' => [
                'title' => $service->title,
                'slug' => $service->slug,
                'concern' => $service->concernName(),
                'audience' => $service->audience,
                'icon' => $service->icon,
                'hero_eyebrow' => $service->hero_eyebrow,
                'hero_title' => $service->hero_title,
                'intro' => $service->intro,
                'hero_image' => Service::imageUrl($service->hero_image),
                'overview_heading' => $service->overview_heading,
                'overview_body' => $service->overview_body,
                'issues_heading' => $service->issues_heading,
                'issues' => $service->issues ?? [],
                'approach_heading' => $service->approach_heading,
                'approach' => $service->approach ?? [],
                'cta_heading' => $service->cta_heading,
                'cta_text' => $service->cta_text,
                'faqs' => $service->faqs ?? [],
            ],
            'otherServices' => Service::published()
                ->whereKeyNot($service->id)
                ->ordered()
                ->limit(3)
                ->get(['title', 'slug', 'summary', 'icon', 'audience']),
            'appointment' => Lead::formOptions(),
        ]);
    }
}
