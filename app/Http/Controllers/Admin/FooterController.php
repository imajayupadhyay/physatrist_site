<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateFooterRequest;
use App\Models\Service;
use App\Models\Setting;
use App\Support\Contact;
use App\Support\Footer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin → Footer: footer content + site-wide contact details.
 */
class FooterController extends Controller
{
    public function edit(): Response
    {
        $footer = Footer::settings();

        return Inertia::render('Footer/Index', [
            'footer' => [
                ...collect($footer)->except('logo')->all(),
                'logo_url' => Service::imageUrl($footer['logo']),
            ],
            'contact' => Contact::settings(),
            'publishedServices' => Service::published()->count(),
        ]);
    }

    public function update(UpdateFooterRequest $request): RedirectResponse
    {
        $current = Footer::settings();
        $data = collect($request->validated())->except(['logo_file', 'remove_logo', 'contact'])->all();

        $logo = $current['logo'];
        if ($request->hasFile('logo_file')) {
            $logo = $request->file('logo_file')->store('branding', 'public');
        } elseif ($request->boolean('remove_logo')) {
            $logo = null;
        }
        if ($current['logo'] && $current['logo'] !== $logo) {
            Storage::disk('public')->delete($current['logo']);
        }

        Setting::put('footer', [...$data, 'logo' => $logo]);
        Setting::put('contact', $request->validated('contact'));

        return back()->with('success', 'Footer saved.');
    }
}
