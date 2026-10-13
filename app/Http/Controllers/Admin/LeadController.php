<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLeadRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Appointment requests from the website form.
 */
class LeadController extends Controller
{
    /**
     * List leads with search and filters.
     */
    public function index(Request $request): Response
    {
        // Current options plus any older values still present on existing leads.
        $concerns = collect(Lead::concernOptions())
            ->merge(Lead::query()->distinct()->orderBy('concern')->pluck('concern'))
            ->unique()
            ->values()
            ->all();

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => in_array($request->query('status'), Lead::statuses(), true) ? $request->query('status') : '',
            'concern' => in_array($request->query('concern'), $concerns, true) ? $request->query('concern') : '',
        ];

        $leads = Lead::query()
            ->search($filters['search'])
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['concern'], fn ($query, $concern) => $query->where('concern', $concern))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Lead $lead) => self::present($lead));

        // Counts per status (respecting search + concern, ignoring the status filter) for the tabs.
        $counts = Lead::query()
            ->search($filters['search'])
            ->when($filters['concern'], fn ($query, $concern) => $query->where('concern', $concern))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('Leads/Index', [
            'leads' => $leads,
            'filters' => $filters,
            'counts' => [
                'all' => $counts->sum(),
                ...collect(Lead::statuses())->mapWithKeys(fn ($status) => [$status => (int) ($counts[$status] ?? 0)]),
            ],
            'statuses' => config('leads.statuses'),
            'concerns' => $concerns,
        ]);
    }

    /**
     * Update a lead's status and/or notes.
     */
    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $lead->update($request->validated());

        return back()->with('success', 'Lead updated.');
    }

    /**
     * Delete a lead.
     */
    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return back()->with('success', 'Lead deleted.');
    }

    /**
     * The fields the admin UI needs for one lead.
     *
     * @return array<string, mixed>
     */
    public static function present(Lead $lead): array
    {
        return [
            'id' => $lead->id,
            'name' => $lead->name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'concern' => $lead->concern,
            'preferred_date' => $lead->preferred_date?->format('D, d M Y'),
            'mode' => $lead->mode,
            'message' => $lead->message,
            'status' => $lead->status,
            'notes' => $lead->notes,
            'source' => $lead->source,
            'created_at' => $lead->created_at->format('d M Y, h:i A'),
            'created_ago' => $lead->created_at->diffForHumans(),
        ];
    }
}
