<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeServicesRequest;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Homepage "Areas of practice" section: heading, intro and lists around the service cards.
 */
class HomeServicesController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('HomeServices/Index', [
            'section' => array_merge(config('content.home_services'), Setting::get('home_services', [])),
            'cards' => Service::published()->where('show_on_home', true)->ordered()->get(['id', 'title', 'icon', 'is_featured']),
        ]);
    }

    public function update(UpdateHomeServicesRequest $request): RedirectResponse
    {
        Setting::put('home_services', $request->validated());

        return back()->with('success', 'Homepage section saved.');
    }
}
