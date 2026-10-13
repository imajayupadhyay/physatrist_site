<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * The website's original header menu, with every service under "Services" (mega menu).
 * Only runs when the menu is empty, so it never overwrites admin changes.
 */
class MenuSeeder extends Seeder
{
    public function run(): void
    {
        if (MenuItem::exists()) {
            return;
        }

        $top = fn (string $label, string $url, array $extra = []) => MenuItem::create(['label' => $label, 'url' => $url, ...$extra]);

        $top('About', '/#about');

        $services = $top('Services', '/#services', ['dropdown_style' => 'mega']);
        Service::ordered()->each(fn (Service $service) => MenuItem::create([
            'parent_id' => $services->id,
            'type' => MenuItem::TYPE_SERVICE,
            'service_id' => $service->id,
        ]));

        $top('Assessments', '/#assessment');
        $top('Videos', '/#videos');
        $top('FAQ', '/#faq');
        $top('Contact', '/#contact');
    }
}
