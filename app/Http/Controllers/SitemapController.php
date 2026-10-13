<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Response;

/**
 * /sitemap.xml: home + every published, indexable service page.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([['loc' => url('/'), 'lastmod' => null, 'priority' => '1.0']])
            ->merge(
                Service::published()
                    ->where('robots_index', true)
                    ->ordered()
                    ->get()
                    // A custom canonical pointing elsewhere means this URL isn't the one to index.
                    ->reject(fn (Service $service) => $service->canonical_url && $service->canonical_url !== $service->url())
                    ->map(fn (Service $service) => [
                        'loc' => $service->url(),
                        'lastmod' => $service->updated_at?->toAtomString(),
                        'priority' => '0.8',
                    ])
            );

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
