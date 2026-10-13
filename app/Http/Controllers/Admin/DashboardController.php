<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard/Index', [
            'leadStats' => [
                'new' => Lead::where('status', Lead::STATUS_NEW)->count(),
                'thisWeek' => Lead::where('created_at', '>=', now()->startOfWeek())->count(),
                'total' => Lead::count(),
            ],
            'latestLeads' => Lead::latest()->limit(5)->get()->map(fn (Lead $lead) => LeadController::present($lead)),
            'statuses' => config('leads.statuses'),
        ]);
    }
}
