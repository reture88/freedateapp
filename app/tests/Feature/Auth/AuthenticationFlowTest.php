<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\DatingProfile;
use App\Models\User;
use App\User\Enum\AccountStatus;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

final class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_guest_can_view_landing_register_login_and_recovery_pages(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('More than a match.')
            ->assertSee('/auth/register', false)
            ->assertSee('/auth/login', false);

        $this->get('/auth/register')
            ->assertOk()
            ->assertSee('Create your account')
            ->assertSee('/terms-of-service', false)
            ->assertSee('/privacy-policy', false);

        $this->get('/auth/login')
            ->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('/forgot-password', false);

        $this->get('/forgot-password')
            ->assertOk()
            ->assertSee('Forgot your password?');

        $this->get('/reset-password/expired')
            ->assertOk()
            ->assertSee('This reset link is no longer valid');
    }

    public function test_terms_and_privacy_pages_are_public(): void
    {
        $this->get('/terms-of-service')
            ->assertOk()
            ->assertSee('Terms of Service')
            ->assertSee('Requires legal review before production use');

        $this->get('/privacy-policy')
            ->assertOk()
            ->assertSee('Privacy Policy')
            ->assertSee('Requires legal review before production use');
    }

    public function test_register_logs_user_in_sends_verification_and_shows_verify_email_state(): void
    {
        Notification::fake();

        $register = $this->post('/auth/register', [
            'name' => 'Test User',
            'email' => 'test.user@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $register->assertRedirect('/auth/verify-email');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'test.user@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get('/auth/verify-email')
            ->assertOk()
            ->assertSee('Check your email')
            ->assertSee('test.user@example.com');
    }

    public function test_authenticated_home_redirects_by_profile_state(): void
    {
        $withoutProfile = $this->createUser('without.profile@example.com');

        $this->actingAs($withoutProfile)
            ->get('/')
            ->assertRedirect('/auth/welcome');

        auth()->logout();

        $withProfile = $this->createUser('with.profile@example.com');
        $this->createProfile($withProfile);

        $this->actingAs($withProfile)
            ->get('/')
            ->assertRedirect('/discovery');
    }

    public function test_login_redirects_to_welcome_or_discovery(): void
    {
        $withoutProfile = $this->createUser('login.new@example.com');

        $this->post('/auth/login', [
            'email' => $withoutProfile->email,
            'password' => 'secret123',
        ])->assertRedirect('/auth/welcome');

        auth()->logout();

        $withProfile = $this->createUser('login.ready@example.com');
        $this->createProfile($withProfile);

        $this->post('/auth/login', [
            'email' => $withProfile->email,
            'password' => 'secret123',
        ])->assertRedirect('/discovery');
    }

    public function test_invalid_login_remains_privacy_safe(): void
    {
        $this->post('/auth/login', [
            'email' => 'missing@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_restricted_account_with_valid_credentials_gets_status_page_after_login(): void
    {
        $restricted = $this->createUser('suspended@example.com', [
            'account_status' => AccountStatus::SUSPENDED,
            'is_active' => false,
        ]);

        $this->post('/auth/login', [
            'email' => $restricted->email,
            'password' => 'secret123',
        ])->assertRedirect('/auth/account-unavailable');

        $this->assertAuthenticatedAs($restricted);

        $this->get('/auth/account-unavailable')
            ->assertOk()
            ->assertSee('Your account is suspended');
    }

    public function test_all_non_active_account_statuses_are_restricted_after_login(): void
    {
        foreach ([AccountStatus::SUSPENDED, AccountStatus::BANNED, AccountStatus::DEACTIVATED, AccountStatus::DELETION_REQUESTED] as $status) {
            $user = $this->createUser(sprintf('%s@example.com', $status->value), [
                'account_status' => $status,
                'is_active' => false,
                'deletion_requested_at' => AccountStatus::DELETION_REQUESTED === $status ? now() : null,
            ]);

            $this->post('/auth/login', [
                'email' => $user->email,
                'password' => 'secret123',
            ])->assertRedirect('/auth/account-unavailable');

            $this->assertAuthenticatedAs($user);

            $this->get('/discovery')
                ->assertRedirect('/auth/account-unavailable');

            auth()->logout();
        }
    }

    public function test_unverified_active_account_cannot_access_product_routes(): void
    {
        $user = $this->createUser('unverified.active@example.com', ['email_verified_at' => null]);
        $this->createProfile($user);

        $this->actingAs($user)
            ->get('/discovery')
            ->assertRedirect('/auth/verify-email');
    }

    public function test_restricted_unverified_account_goes_to_account_status_before_verification(): void
    {
        $user = $this->createUser('unverified.suspended@example.com', [
            'account_status' => AccountStatus::SUSPENDED,
            'is_active' => false,
            'email_verified_at' => null,
        ]);
        $this->createProfile($user);

        $this->actingAs($user)
            ->get('/discovery')
            ->assertRedirect('/auth/account-unavailable');
    }

    public function test_user_can_resend_verification_email(): void
    {
        Notification::fake();
        $user = $this->createUser('resend@example.com', ['email_verified_at' => null]);

        $this->actingAs($user)
            ->post('/auth/email/verification-notification')
            ->assertRedirect();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_resend_verification_email_is_throttled(): void
    {
        $this->withMiddleware(ThrottleRequests::class);
        Notification::fake();
        $user = $this->createUser('resend.throttle@example.com', ['email_verified_at' => null]);
        RateLimiter::clear(sha1((string) $user->getAuthIdentifier()));

        for ($attempt = 0; $attempt < 6; ++$attempt) {
            $this->actingAs($user)
                ->post('/auth/email/verification-notification')
                ->assertRedirect();
        }

        $this->actingAs($user)
            ->post('/auth/email/verification-notification')
            ->assertTooManyRequests();
    }

    public function test_valid_verification_redirects_unprofiled_user_to_welcome(): void
    {
        $user = $this->createUser('verify.no-profile@example.com', ['email_verified_at' => null]);
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->getKey(),
            'hash' => sha1((string) $user->email),
        ]);

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect('/auth/welcome');

        $this->assertTrue($user->fresh()?->hasVerifiedEmail());
    }

    public function test_valid_verification_redirects_profiled_user_to_discovery(): void
    {
        $user = $this->createUser('verify.profile@example.com', ['email_verified_at' => null]);
        $this->createProfile($user);
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->getKey(),
            'hash' => sha1((string) $user->email),
        ]);

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect('/discovery');

        $this->assertTrue($user->fresh()?->hasVerifiedEmail());
    }

    public function test_invalid_verification_signature_is_rejected(): void
    {
        $user = $this->createUser('verify.invalid@example.com', ['email_verified_at' => null]);

        $this->actingAs($user)
            ->get(sprintf('/auth/email/verify/%d/%s', $user->getKey(), sha1((string) $user->email)))
            ->assertForbidden();

        $this->assertFalse($user->fresh()?->hasVerifiedEmail());
    }

    public function test_already_verified_user_leaves_verification_prompt(): void
    {
        $user = $this->createUser('already.verified@example.com');

        $this->actingAs($user)
            ->get('/auth/verify-email')
            ->assertRedirect('/auth/welcome');
    }

    public function test_forgot_password_uses_privacy_safe_confirmation(): void
    {
        Notification::fake();
        $user = $this->createUser('recover@example.com');

        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);

        $this->post('/forgot-password', [
            'email' => 'unknown@example.com',
        ])->assertSessionHas('status');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = $this->createUser('reset@example.com');
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ])->assertRedirect('/auth/login');

        $user->refresh();
        $this->assertTrue(Hash::check('newsecret123', (string) $user->password));
    }

    public function test_invalid_reset_token_routes_to_expired_state(): void
    {
        $user = $this->createUser('expired@example.com');

        $this->post('/reset-password', [
            'token' => 'not-a-real-token',
            'email' => $user->email,
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ])->assertRedirect('/reset-password/expired');
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createUser(string $email, array $overrides = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'MTAM User',
            'email' => $email,
            'password' => Hash::make('secret123'),
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
            'email_verified_at' => now(),
        ], $overrides));
    }

    private function createProfile(User $user): DatingProfile
    {
        return DatingProfile::query()->create([
            'user_id' => $user->getKey(),
            'display_name' => $user->name,
            'birthdate' => '1992-01-01',
            'gender' => 'woman',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'relationship_intent' => 'long_term',
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => false,
            'completed_at' => '2026-07-19 12:00:00',
        ]);
    }
}
