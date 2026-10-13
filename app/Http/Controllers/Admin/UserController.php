<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin accounts CRUD. The logged-in admin manages their own account
 * through AccountController instead, so self-edit / self-delete is blocked here.
 */
class UserController extends Controller
{
    /**
     * List admin accounts.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));

        $admins = Admin::query()
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Admin $admin) => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => $admin->role,
                'created_at' => $admin->created_at?->format('d M Y'),
            ]);

        return Inertia::render('Users/Index', [
            'admins' => $admins,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Create an admin account.
     */
    public function store(StoreAdminRequest $request): RedirectResponse
    {
        Admin::create([
            ...$request->validated(),
            'role' => Admin::ROLE_ADMIN,
        ]);

        return back()->with('success', 'Admin account created.');
    }

    /**
     * Update another admin's account.
     */
    public function update(UpdateAdminRequest $request, Admin $admin): RedirectResponse
    {
        abort_if($admin->is($request->user('admin')), 403, 'Use "My account" to edit your own account.');

        $data = $request->validated();

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $admin->update($data);

        return back()->with('success', 'Admin account updated.');
    }

    /**
     * Delete another admin's account.
     */
    public function destroy(Request $request, Admin $admin): RedirectResponse
    {
        abort_if($admin->is($request->user('admin')), 403, 'You cannot delete your own account.');

        $admin->delete();

        return back()->with('success', 'Admin account deleted.');
    }
}
