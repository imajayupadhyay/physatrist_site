<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Create (or reset) the default admin account.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin', 'role' => Admin::ROLE_ADMIN, 'password' => 'admin@123'],
        );
    }
}
