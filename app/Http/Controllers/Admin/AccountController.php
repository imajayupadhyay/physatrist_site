<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAccountRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;

/**
 * The logged-in admin's own account ("My account" tab on the Users page).
 */
class AccountController extends Controller
{
    /**
     * Update the admin's name and email.
     */
    public function update(UpdateAccountRequest $request): RedirectResponse
    {
        $request->user('admin')->update($request->validated());

        return back()->with('success', 'Your details have been updated.');
    }

    /**
     * Change the admin's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user('admin')->update([
            'password' => $request->validated('password'),
        ]);

        return back()->with('success', 'Your password has been changed.');
    }
}
