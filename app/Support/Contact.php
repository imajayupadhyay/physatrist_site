<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Site-wide contact details (Admin → Footer → Contact & social).
 */
class Contact
{
    /**
     * @return array<string, mixed>
     */
    public static function settings(): array
    {
        return array_merge(config('content.contact'), Setting::get('contact', []));
    }

    /**
     * The `contact` prop: same keys as resources/js/Config/site.js, which it updates.
     *
     * @return array<string, mixed>
     */
    public static function forWebsite(): array
    {
        $c = self::settings();

        return [
            'name' => config('seo.site_name'),
            'url' => $c['website_url'],
            'email' => $c['email'],
            'phones' => collect($c['phones'])->map(fn ($phone) => [
                'display' => $phone,
                'tel' => self::tel($phone),
            ])->values()->all(),
            'whatsappNumber' => $c['whatsapp_number'],
            'hours' => $c['hours'],
            'hoursShort' => $c['hours_short'] ?: $c['hours'],
            'address' => $c['address'],
            'mapsUrl' => $c['maps_url'],
            'instagram' => $c['instagram'],
            'youtube' => $c['youtube'],
            'facebook' => $c['facebook'],
            'linkedin' => $c['linkedin'],
        ];
    }

    /**
     * "+91 82853 54608" → "+918285354608"
     */
    public static function tel(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        return (str_starts_with(trim($phone), '+') ? '+' : '').$digits;
    }
}
