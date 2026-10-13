<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Service;
use App\Models\Setting;
use App\Support\HomeContent;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Show the home page.
     */
    public function __invoke(): Response
    {
        return Inertia::render('Home/Index', [
            'seo' => Seo::home(),
            // Every editable section (Admin → Home page).
            'home' => HomeContent::all(),
            // "Areas of practice" section: editable text + the service cards.
            'servicesSection' => array_merge(config('content.home_services'), Setting::get('home_services', [])),
            'services' => Service::published()
                ->where('show_on_home', true)
                ->ordered()
                ->get()
                ->map(fn (Service $service) => [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'audience' => $service->audience,
                    'summary' => $service->summary,
                    'icon' => $service->icon,
                    'is_featured' => $service->is_featured,
                ]),
            'appointment' => Lead::formOptions(),
        ]);
    }
}
