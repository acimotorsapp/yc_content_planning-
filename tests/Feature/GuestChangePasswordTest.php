<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * The login screen offers a "Change password" route for people who know their
 * current password but cannot receive a reset email.
 */
class GuestChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $password = '123456'): User
    {
        return User::factory()->create([
            'email' => 'staff@aci-bd.com',
            'role' => 'product_team',
            'email_verified_at' => null,
            'password' => Hash::make($password),
        ]);
    }

    private function submit(array $overrides = [])
    {
        return $this->post('/change-password', array_merge([
            'email' => 'staff@aci-bd.com',
            'current_password' => '123456',
            'password' => 'my-new-password',
            'password_confirmation' => 'my-new-password',
        ], $overrides));
    }

    public function test_the_login_page_links_to_it(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee(route('password.change'), false);
        $response->assertSee('Change password', false);
    }

    public function test_a_guest_can_open_the_page(): void
    {
        $response = $this->get('/change-password');

        $response->assertStatus(200);
        $response->assertSee('Current Password', false);
        $response->assertSee('New Password', false);
    }

    public function test_the_password_changes_with_the_correct_current_one(): void
    {
        $user = $this->user();

        $this->submit()
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('my-new-password', $user->refresh()->password));
        // Changing a password must not sign anyone in.
        $this->assertGuest();
    }

    public function test_the_new_password_works_at_login_and_the_old_one_does_not(): void
    {
        $user = $this->user();

        $this->submit();

        $this->post('/login', ['email' => $user->email, 'password' => '123456'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'my-new-password'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_wrong_current_password_is_rejected(): void
    {
        $user = $this->user();

        $this->submit(['current_password' => 'not-my-password'])
            ->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('123456', $user->refresh()->password), 'The password must be unchanged');
    }

    public function test_an_unknown_email_gives_the_same_message_so_addresses_cannot_be_probed(): void
    {
        $this->user();

        $unknown = $this->submit(['email' => 'nobody@aci-bd.com']);
        $wrongPassword = $this->submit(['current_password' => 'not-my-password']);

        $this->assertSame(
            $unknown->getSession()->get('errors')->get('email'),
            $wrongPassword->getSession()->get('errors')->get('email'),
            'A missing account and a wrong password must look identical'
        );
    }

    public function test_the_confirmation_must_match(): void
    {
        $user = $this->user();

        $this->submit(['password_confirmation' => 'something-else'])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('123456', $user->refresh()->password));
    }

    public function test_a_short_new_password_is_rejected(): void
    {
        $user = $this->user();

        $this->submit(['password' => 'abc', 'password_confirmation' => 'abc'])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('123456', $user->refresh()->password));
    }

    public function test_repeated_wrong_attempts_are_throttled(): void
    {
        $user = $this->user();
        RateLimiter::clear('change-password|staff@aci-bd.com|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->submit(['current_password' => 'wrong'])->assertSessionHasErrors('email');
        }

        // The sixth attempt is locked out even though the details are now correct.
        $this->submit()->assertSessionHasErrors('email');

        $this->assertTrue(
            Hash::check('123456', $user->refresh()->password),
            'A throttled request must not change the password'
        );
    }

    public function test_a_signed_in_user_is_sent_to_the_dashboard_instead(): void
    {
        $user = User::factory()->create(['role' => 'super_admin', 'email_verified_at' => now()]);

        $this->actingAs($user)->get('/change-password')->assertRedirect('/dashboard');
    }
}
