<?php

/*
| Admin routes, loaded from bootstrap/app.php with:
|   - the "web" middleware group
|   - URL prefix  /secure-admin
|   - name prefix admin.
*/

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\HeaderController;
use App\Http\Controllers\Admin\HomePageController;
use App\Http\Controllers\Admin\HomeServicesController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Leads: appointment requests from the website form
    Route::resource('leads', LeadController::class)->only(['index', 'update', 'destroy']);

    // Home page: every homepage section (except Services + the booking form), saved one section at a time
    Route::get('/home', [HomePageController::class, 'edit'])->name('home.edit');
    Route::put('/home/{section}', [HomePageController::class, 'update'])
        ->whereIn('section', array_keys(\App\Support\HomeContent::SECTIONS))
        ->name('home.update');

    // Services: homepage cards, detail pages, SEO + the homepage section text
    Route::get('/services/homepage', [HomeServicesController::class, 'edit'])->name('services.homepage');
    Route::put('/services/homepage', [HomeServicesController::class, 'update'])->name('services.homepage.update');
    Route::resource('services', ServiceController::class)->except(['show']);
    Route::post('/services/{service}/move', [ServiceController::class, 'move'])->name('services.move');
    Route::patch('/services/{service}/toggle', [ServiceController::class, 'toggle'])->name('services.toggle');

    // Header: logo, CTA, mega-menu promo + menu items
    Route::get('/header', [HeaderController::class, 'edit'])->name('header.edit');
    Route::put('/header', [HeaderController::class, 'update'])->name('header.update');
    Route::post('/header/menu', [MenuItemController::class, 'store'])->name('menu.store');
    Route::put('/header/menu/{menuItem}', [MenuItemController::class, 'update'])->name('menu.update');
    Route::delete('/header/menu/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu.destroy');
    Route::post('/header/menu/{menuItem}/move', [MenuItemController::class, 'move'])->name('menu.move');
    Route::patch('/header/menu/{menuItem}/toggle', [MenuItemController::class, 'toggle'])->name('menu.toggle');
    Route::post('/header/menu/{menuItem}/services', [MenuItemController::class, 'addServices'])->name('menu.services');

    // Footer: content, link columns + site-wide contact details
    Route::get('/footer', [FooterController::class, 'edit'])->name('footer.edit');
    Route::put('/footer', [FooterController::class, 'update'])->name('footer.update');

    // Users: admin accounts CRUD + the logged-in admin's own account
    Route::resource('users', UserController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['users' => 'admin']);
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
