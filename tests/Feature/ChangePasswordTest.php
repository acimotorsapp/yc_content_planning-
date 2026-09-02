<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Signed-in users change their own password by supplying the current one.
 * No email verification, no reset link.
 */
class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    private function unverifiedUser(string $password = '123456'): User
    {
        return User::factory()->create([
            'role' => 'product_team',
            'email_verified_at' => null,
            'password' => Hash::make($password),
        ]);
    }

    public function test_an_unverified_user_can_open_the_change_password_page(): void
    {
        $user = $this->unverifiedUser();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Current Password', false);
    }

    public function test_an_unverified_user_can_change_their_password_with_the_current_one(): void
    {
        $user = $this->unverifiedUser();

        $response = $this->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => '123456',
                'password' => 'my-new-password',
                'password_confirmation' => 'my-new-password',
            ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertTrue(Hash::check('my-new-password', $user->refresh()->password));
        $this->assertNull($user->refresh()->email_verified_at, 'Changing a password must not require or grant verification');
    }

    public function test_the_new_password_actually_works_at_login(): void
    {
        $user = $this->unverifiedUser();

        $this->actingAs($user)->put('/password', [
            'current_password' => '123456',
            'password' => 'my-new-password',
            'password_confirmation' => 'my-new-password',
        ])->assertSessionHasNoErrors();

        $this->post('/logout');

        $this->post('/login', ['email' => $user->email, 'password' => '123456'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'my-new-password'])
            ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_wrong_current_password_is_rejected(): void
    {
        $user = $this->unverifiedUser();

        $this->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'not-my-password',
                'password' => 'my-new-password',
                'password_confirmation' => 'my-new-password',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('123456', $user->refresh()->password), 'The password must be unchanged');
    }

    public function test_the_current_password_is_required(): void
    {
        $user = $this->unverifiedUser();

        $this->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'password' => 'my-new-password',
                'password_confirmation' => 'my-new-password',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');

        $this->assertTrue(Hash::check('123456', $user->refresh()->password));
    }

    public function test_the_confirmation_must_match(): void
    {
        $user = $this->unverifiedUser();

        $this->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => '123456',
                'password' => 'my-new-password',
                'password_confirmation' => 'something-else',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'password');

        $this->assertTrue(Hash::check('123456', $user->refresh()->password));
    }

    public function test_a_guest_cannot_change_a_password(): void
    {
        $this->put('/password', [
            'current_password' => '123456',
            'password' => 'my-new-password',
            'password_confirmation' => 'my-new-password',
        ])->assertRedirect('/login');
    }
}
