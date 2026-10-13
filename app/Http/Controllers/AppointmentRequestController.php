<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Receives the website's "Book an appointment" form and stores it as a lead.
 */
class AppointmentRequestController extends Controller
{
    public function store(StoreLeadRequest $request): RedirectResponse
    {
        // Honeypot: real visitors never see or fill the "website" field.
        // Pretend it worked so bots get no signal.
        if (filled($request->input('website'))) {
            return back();
        }

        $key = 'leads:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, config('leads.rate_limit_per_hour'))) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            throw ValidationException::withMessages([
                'form' => "You've sent several requests already. Please try again in {$minutes} minutes, or call us directly.",
            ]);
        }

        $data = $request->validated();

        // Double-click / resubmit protection: same phone and concern a moment ago → don't store twice.
        $isDuplicate = Lead::query()
            ->where('phone', $data['phone'])
            ->where('concern', $data['concern'])
            ->where('created_at', '>=', now()->subMinutes(config('leads.duplicate_window_minutes')))
            ->exists();

        if (! $isDuplicate) {
            RateLimiter::hit($key, 3600);

            Lead::create([
                ...$data,
                'source' => 'homepage_appointment',
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 497),
            ]);
        }

        return back();
    }
}
