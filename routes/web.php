<?php

use App\Http\Controllers\AppointmentRequestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/services', fn () => redirect('/#services'));
Route::get('/services/{slug}', [ServiceController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-_]+')
    ->name('services.show');

Route::post('/appointment-requests', [AppointmentRequestController::class, 'store'])->name('appointment-requests.store');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
