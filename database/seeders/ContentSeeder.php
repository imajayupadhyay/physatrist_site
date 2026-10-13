<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Default editable site content. Only creates missing keys, so admin edits are kept.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        if (! Setting::where('key', 'home_services')->exists()) {
            Setting::put('home_services', config('content.home_services'));
        }
    }
}
