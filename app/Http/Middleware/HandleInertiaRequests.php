<?php

namespace App\Http\Middleware;

use App\Models\Lead;
use App\Support\Contact;
use App\Support\Footer;
use App\Support\Header;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Admin pages use their own root template (and Vite entry).
     */
    public function rootView(Request $request): string
    {
        return $request->routeIs('admin.*') ? 'admin.app' : $this->rootView;
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'admin' => fn () => $request->user('admin')?->only('id', 'name', 'email', 'role'),
            ],
            // Website header: logo, menu (with dropdowns), CTA. Managed in Admin → Header.
            'header' => fn () => $request->routeIs('admin.*') ? null : Header::forWebsite(),
            // Website footer (Admin → Footer).
            'footer' => fn () => $request->routeIs('admin.*') ? null : Footer::forWebsite(),
            // Site-wide contact details; updates resources/js/Config/site.js in the browser.
            'contact' => fn () => $request->routeIs('admin.*') ? null : Contact::forWebsite(),
            // Sidebar badge in the admin panel.
            'newLeadsCount' => fn () => $request->routeIs('admin.*') && $request->user('admin')
                ? Lead::where('status', Lead::STATUS_NEW)->count()
                : null,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
