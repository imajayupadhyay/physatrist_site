<?php

/*
| Appointment leads: single source of truth for the options shown on the
| website form and accepted by the server. Update here only.
*/

return [

    // "Area of concern" options come from the published services (Lead::concernOptions()).

    'modes' => ['In-clinic', 'Online'],

    // key => label (order = pipeline order in the admin)
    'statuses' => [
        'new' => 'New',
        'contacted' => 'Contacted',
        'booked' => 'Booked',
        'closed' => 'Closed',
    ],

    // Max submissions per IP address per hour.
    'rate_limit_per_hour' => 5,

    // Same phone + concern within this many minutes is treated as a duplicate (double submit).
    'duplicate_window_minutes' => 10,

];
