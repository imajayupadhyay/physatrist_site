<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHeaderRequest;
use App\Models\MenuItem;
use App\Models\Service;
use App\Models\Setting;
use App\Support\Contact;
use App\Support\Header;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin → Header: top bar, logo, CTA button, mega-menu promo and the menu editor.
 */
class HeaderController extends Controller
{
    public function edit(): Response
    {
        $settings = Header::settings();

        return Inertia::render('Header/Index', [
            'settings' => [
                ...collect($settings)->except('logo')->all(),
                'logo_url' => Service::imageUrl($settings['logo']),
            ],
            'menu' => MenuItem::topLevel()->ordered()->with(['service', 'children.service'])->get()
                ->map(fn (MenuItem $item) => [
                    ...self::presentItem($item),
                    'children' => $item->children->map(fn (MenuItem $child) => self::presentItem($child))->values(),
                ]),
            'services' => Service::ordered()->get(['id', 'title', 'slug', 'icon', 'is_published']),
            // Read-only in the Top bar tab (edited in Footer → Contact & social).
            'contact' => Contact::forWebsite(),
        ]);
    }

    public function update(UpdateHeaderRequest $request): RedirectResponse
    {
        $settings = Header::settings();
        $data = collect($request->validated())->except(['logo_file', 'remove_logo'])->all();

        $logo = $settings['logo'];
        if ($request->hasFile('logo_file')) {
            $logo = $request->file('logo_file')->store('branding', 'public');
        } elseif ($request->boolean('remove_logo')) {
            $logo = null;
        }
        if ($settings['logo'] && $settings['logo'] !== $logo) {
            Storage::disk('public')->delete($settings['logo']);
        }

        Setting::put('header', [...$data, 'logo' => $logo]);

        return back()->with('success', 'Header settings saved.');
    }

    /**
     * @return array<string, mixed>
     */
    public static function presentItem(MenuItem $item): array
    {
        return [
            'id' => $item->id,
            'parent_id' => $item->parent_id,
            'type' => $item->type,
            'service_id' => $item->service_id,
            'label' => $item->label,
            'display_label' => $item->displayLabel(),
            'url' => $item->url,
            'href' => $item->href(),
            'description' => $item->description,
            'dropdown_style' => $item->dropdown_style,
            'open_in_new_tab' => $item->open_in_new_tab,
            'is_visible' => $item->is_visible,
            // Visible in admin but hidden on the website because its service isn't published.
            'service_unpublished' => $item->isService() && ! $item->service?->is_published,
            'icon' => $item->service?->icon,
        ];
    }
}
