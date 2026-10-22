<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->admin()->create();

        $this->post(route('login.authenticate'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))->post(route('login.authenticate'), [
            'email' => $user->email,
            'password' => 'salah',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registered_user_becomes_petugas(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'rahasia',
        ])->assertRedirect(route('home'));

        $user = User::firstWhere('email', 'budi@example.com');

        $this->assertSame(Role::Petugas, $user->role);
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_user_cannot_see_login_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect(route('home'));
    }

    public function test_logout_requires_post(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/logout')->assertMethodNotAllowed();

        $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_can_update_profile_and_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'baru@example.com',
            'current_password' => 'password',
            'new_password' => 'baru123',
            'password_confirmation' => 'baru123',
        ])->assertRedirect(route('profile'))->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertSame('Nama Baru', $user->name);
        $this->assertTrue(password_verify('baru123', $user->password));
    }

    public function test_profile_password_change_requires_correct_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'salah',
            'new_password' => 'baru123',
            'password_confirmation' => 'baru123',
        ])->assertSessionHasErrors('current_password');
    }
}
