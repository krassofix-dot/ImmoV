<?php

namespace Tests\Feature;

use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/')->assertRedirect('/dashboard');
        $this->get('/login')->assertOk()->assertSee('Anmelden');
    }

    public function test_active_user_can_log_in_and_login_is_audited(): void
    {
        $user = $this->userWithRole('mitarbeiter', ['email' => 'ma@example.com']);

        $this->post('/login', ['email' => 'MA@example.com', 'password' => 'password'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $this->assertNotNull($user->fresh()->last_login_at);
        $this->assertTrue(Audit::where('event', 'login')->where('auditable_id', $user->id)->exists());
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $this->userWithRole('mitarbeiter', ['email' => 'off@example.com', 'is_active' => false]);

        $this->post('/login', ['email' => 'off@example.com', 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_deactivated_user_is_logged_out_on_next_request(): void
    {
        $user = $this->userWithRole('mitarbeiter');
        $this->actingAs($user)->get('/dashboard')->assertOk();

        $user->update(['is_active' => false]);
        $this->actingAs($user->fresh())->get('/dashboard')->assertRedirect('/login');
    }

    public function test_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_two_factor_can_be_enabled_and_secret_is_not_audited(): void
    {
        $user = $this->userWithRole('mitarbeiter');

        $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post('/user/two-factor-authentication')
            ->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
        $this->actingAs($user)->get('/profile')->assertOk()->assertSee('<svg', false);

        foreach (Audit::where('auditable_type', 'user')->get() as $audit) {
            $this->assertArrayNotHasKey('two_factor_secret', $audit->new_values ?? []);
            $this->assertArrayNotHasKey('password', $audit->new_values ?? []);
        }
    }
}
