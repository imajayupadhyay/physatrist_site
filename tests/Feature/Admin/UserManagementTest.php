<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $me;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->me = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin@123',
        ]);
    }

    private function makeAdmin(string $email = 'other@example.com'): Admin
    {
        return Admin::create(['name' => 'Other Admin', 'email' => $email, 'password' => 'secret123']);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/secure-admin/users')->assertRedirect('/secure-admin');
        $this->post('/secure-admin/users', [])->assertRedirect('/secure-admin');
    }

    public function test_users_page_lists_admins(): void
    {
        $this->makeAdmin();

        $this->actingAs($this->me, 'admin')
            ->get('/secure-admin/users')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Users/Index', false)
                ->has('admins.data', 2)
                ->where('admins.data.0.role', 'admin')
                ->where('auth.admin.email', 'admin@gmail.com'));
    }

    public function test_search_filters_admins(): void
    {
        $this->makeAdmin('jane@example.com');

        $this->actingAs($this->me, 'admin')
            ->get('/secure-admin/users?search=jane')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Users/Index', false)
                ->has('admins.data', 1)
                ->where('admins.data.0.email', 'jane@example.com')
                ->where('filters.search', 'jane'));
    }

    public function test_admin_can_create_an_admin_with_the_admin_role(): void
    {
        $this->actingAs($this->me, 'admin')
            ->from('/secure-admin/users')
            ->post('/secure-admin/users', [
                'name' => 'New Admin',
                'email' => 'new@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect('/secure-admin/users')
            ->assertSessionHas('success');

        $created = Admin::where('email', 'new@example.com')->firstOrFail();
        $this->assertSame('admin', $created->role);
        $this->assertTrue(Hash::check('password123', $created->password));
    }

    public function test_create_validates_input(): void
    {
        $this->actingAs($this->me, 'admin')
            ->from('/secure-admin/users')
            ->post('/secure-admin/users', [
                'name' => '',
                'email' => 'admin@gmail.com', // already taken
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertSame(1, Admin::count());
    }

    public function test_role_cannot_be_set_from_the_request(): void
    {
        $this->actingAs($this->me, 'admin')->post('/secure-admin/users', [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'role' => 'superadmin',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertSame('admin', Admin::where('email', 'sneaky@example.com')->value('role'));
    }

    public function test_admin_can_update_another_admin_and_keep_password_when_blank(): void
    {
        $other = $this->makeAdmin();
        $oldHash = $other->password;

        $this->actingAs($this->me, 'admin')
            ->from('/secure-admin/users')
            ->put("/secure-admin/users/{$other->id}", [
                'name' => 'Renamed',
                'email' => 'renamed@example.com',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect('/secure-admin/users')
            ->assertSessionHasNoErrors();

        $other->refresh();
        $this->assertSame('Renamed', $other->name);
        $this->assertSame('renamed@example.com', $other->email);
        $this->assertSame($oldHash, $other->password);
    }

    public function test_admin_can_reset_another_admins_password(): void
    {
        $other = $this->makeAdmin();

        $this->actingAs($this->me, 'admin')->put("/secure-admin/users/{$other->id}", [
            'name' => $other->name,
            'email' => $other->email,
            'password' => 'brandnew123',
            'password_confirmation' => 'brandnew123',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('brandnew123', $other->fresh()->password));
    }

    public function test_admin_can_delete_another_admin(): void
    {
        $other = $this->makeAdmin();

        $this->actingAs($this->me, 'admin')
            ->from('/secure-admin/users')
            ->delete("/secure-admin/users/{$other->id}")
            ->assertRedirect('/secure-admin/users')
            ->assertSessionHas('success');

        $this->assertModelMissing($other);
    }

    public function test_admin_cannot_edit_or_delete_themselves_through_crud(): void
    {
        $this->actingAs($this->me, 'admin')
            ->delete("/secure-admin/users/{$this->me->id}")
            ->assertForbidden();

        $this->actingAs($this->me, 'admin')
            ->put("/secure-admin/users/{$this->me->id}", ['name' => 'X', 'email' => 'x@example.com'])
            ->assertForbidden();

        $this->assertModelExists($this->me);
    }

    public function test_admin_can_update_own_details(): void
    {
        $this->actingAs($this->me, 'admin')
            ->from('/secure-admin/users')
            ->put('/secure-admin/account', ['name' => 'Lovleena', 'email' => 'me@example.com'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertSame('me@example.com', $this->me->fresh()->email);
    }

    public function test_own_email_must_be_unique(): void
    {
        $this->makeAdmin('taken@example.com');

        $this->actingAs($this->me, 'admin')
            ->put('/secure-admin/account', ['name' => 'Admin', 'email' => 'taken@example.com'])
            ->assertSessionHasErrors('email');
    }

    public function test_admin_can_change_own_password_with_current_password(): void
    {
        $this->actingAs($this->me, 'admin')
            ->from('/secure-admin/users')
            ->put('/secure-admin/account/password', [
                'current_password' => 'admin@123',
                'password' => 'newpass123',
                'password_confirmation' => 'newpass123',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('newpass123', $this->me->fresh()->password));
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $this->actingAs($this->me, 'admin')
            ->put('/secure-admin/account/password', [
                'current_password' => 'wrong-password',
                'password' => 'newpass123',
                'password_confirmation' => 'newpass123',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('admin@123', $this->me->fresh()->password));
    }
}
