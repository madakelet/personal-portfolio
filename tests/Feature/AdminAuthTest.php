<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
    }

    public function test_owner_can_log_in_and_reach_the_admin(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse-battery']);

        $this->post('/login', ['email' => $user->email, 'password' => 'correct-horse-battery'])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Home')
            ->where('auth.user.email', $user->email));
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse-battery']);

        $this->from('/login')
            ->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_owner_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout');

        $this->assertGuest();
    }

    public function test_there_is_no_registration_or_password_reset(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/forgot-password')->assertNotFound();
    }

    public function test_make_owner_creates_then_updates_a_single_account(): void
    {
        $this->artisan('make:owner', ['--name' => 'Owner', '--email' => 'me@example.test', '--password' => 'long-enough-password'])
            ->assertSuccessful();
        $this->artisan('make:owner', ['--name' => 'Owner', '--email' => 'me@example.test', '--password' => 'another-long-password'])
            ->assertSuccessful();

        $this->assertSame(1, User::count());
        $this->post('/login', ['email' => 'me@example.test', 'password' => 'another-long-password'])->assertRedirect('/admin');
    }

    public function test_make_owner_rejects_short_passwords(): void
    {
        $this->artisan('make:owner', ['--name' => 'Owner', '--email' => 'me@example.test', '--password' => 'short'])
            ->assertFailed();

        $this->assertSame(0, User::count());
    }
}
