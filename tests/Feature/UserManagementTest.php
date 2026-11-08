<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_admin_creates_user_with_role(): void
    {
        $admin = $this->userWithRole('administrator');

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Neu', 'email' => 'Neu@Example.com', 'password' => 'Sicher12345', 'password_confirmation' => 'Sicher12345',
            'locale' => 'en', 'role' => 'mitarbeiter',
        ])->assertRedirect('/admin/users');

        $user = User::where('email', 'neu@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('mitarbeiter'));
        $this->assertDatabaseHas('audits', ['event' => 'roles_updated', 'auditable_id' => $user->id]);
    }

    public function test_weak_passwords_are_rejected(): void
    {
        $admin = $this->userWithRole('administrator');

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Neu', 'email' => 'neu@example.com', 'password' => 'kurz', 'password_confirmation' => 'kurz',
            'locale' => 'de', 'role' => 'mitarbeiter',
        ])->assertSessionHasErrors('password');
    }

    public function test_last_admin_cannot_be_demoted_or_deactivated(): void
    {
        $admin = $this->userWithRole('administrator');

        $this->actingAs($admin)->put("/admin/users/{$admin->id}", [
            'name' => $admin->name, 'email' => $admin->email, 'locale' => 'de', 'role' => 'mitarbeiter', 'is_active' => '1',
        ])->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->hasRole('administrator'));
    }

    public function test_admin_can_deactivate_another_user(): void
    {
        $admin = $this->userWithRole('administrator');
        $other = $this->userWithRole('mitarbeiter');

        $this->actingAs($admin)->put("/admin/users/{$other->id}", [
            'name' => $other->name, 'email' => $other->email, 'locale' => 'de', 'role' => 'mitarbeiter', 'is_active' => '0',
        ])->assertRedirect('/admin/users');

        $this->assertFalse($other->fresh()->is_active);
    }

    public function test_create_admin_command(): void
    {
        $this->artisan('immov:create-admin', ['email' => 'chef@example.com', '--name' => 'Chef'])
            ->expectsQuestion('Passwort (mind. 10 Zeichen, Buchstaben und Ziffern)', 'Sicher12345')
            ->expectsQuestion('Passwort wiederholen', 'Sicher12345')
            ->assertSuccessful();

        $this->assertTrue(User::where('email', 'chef@example.com')->first()->hasRole('administrator'));
    }
}
