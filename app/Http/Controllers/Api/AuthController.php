<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Passport\TokenRepository;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\RefreshTokenRepository;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\BaseApiController;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class AuthController extends BaseApiController
{
    private const OTP_LENGTH = 6;
    private const OTP_TTL_MINUTES = 10;
    private const OTP_RESEND_COOLDOWN_SECONDS = 60;

    /**
     * Register a new user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'device_name' => 'required|string',
                'terms_accepted' => 'required|boolean|accepted',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(), // Auto-verify for mobile app
            ]);

            // Assign default customer role
            Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
            $user->syncRoles('customer');

            // Assign default free subscription
            $free_plan = Plan::where('type', 'free')->where('price', 0)->where('is_active', true)->first();
            if (!$free_plan) {
                $free_plan = Plan::firstOrCreate(
                    ['slug' => 'free-plan'],
                    [
                        'name' => 'Free Plan',
                        'description' => 'Start your fitness journey with basic features and limited content access.',
                        'price' => 0.00,
                        'interval' => 'month',
                        'type' => 'free',
                        'stripe_plan_id' => null,
                        'features' => [
                            'Access to basic workouts',
                            'Limited meal plans',
                            'Basic progress tracking',
                            'Community support',
                        ],
                        'is_active' => true,
                        'is_popular' => false,
                        'sort_order' => 1,
                    ]
                );
            }

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $free_plan->id,
                'stripe_id' => 'free_' . uniqid(),
                'stripe_status' => 'active',
                'status' => 'active',
            ]);

            // Create API token with Passport
            $tokenResult = $user->createToken($request->device_name);
            return response()->json($tokenResult);

            $token = $tokenResult->accessToken;

            return $this->success([
                'user' => $this->formatUserData($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token->expires_at,
                'onboarding_completed' => false,
            ], 'Registration successful');

        } catch (\Exception $e) {
            return $this->serverError($e->getMessage()?:'Registration failed. Please try again.');
        }
    }

    /**
     * Login user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->unauthorized('Invalid credentials');
            }

            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active ?? true) {
                return $this->forbidden('Account is deactivated. Please contact support.');
            }

            // Revoke previous tokens for security (optional)
            // $user->tokens()->delete();

            // Create new token with Passport
            $tokenResult = $user->createToken($request->device_name);
            $token = $tokenResult->accessToken;

            // Check if onboarding is completed
            $onboardingCompleted = $user->userProfile !== null;

            return $this->success([
                'user' => $this->formatUserData($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token->expires_at,
                'onboarding_completed' => $onboardingCompleted,
            ], 'Login successful');

        } catch (\Exception $e) {
            return $this->serverError($e->getMessage() ?: 'Login failed. Please try again.');
        }
    }

    /**
     * Guest login.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function guestLogin(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $guestEmail = 'guest@fitwnata.com';
            $user = User::where('email', $guestEmail)->first();

            if (!$user) {
                $user = User::create([
                    'name' => 'Guest User',
                    'email' => $guestEmail,
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                ]);

                Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
                $user->syncRoles('customer');
            }

            if (!$user->is_active ?? true) {
                return $this->forbidden('Account is deactivated. Please contact support.');
            }

            $tokenResult = $user->createToken($request->device_name);
            $token = $tokenResult->accessToken;

            return $this->success([
                'user' => $this->formatUserData($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token->expires_at,
                'onboarding_completed' => false,
            ], 'Guest login successful');
        } catch (\Exception $e) {
            return $this->serverError('Guest login failed. Please try again.');
        }
    }

    /**
     * Social login (Google, Facebook, Apple).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function socialLogin(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'provider' => 'required|in:google,facebook,apple',
                'social_id' => 'required|string',
                'email' => 'nullable|email',
                'name' => 'required|string',
                'device_name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }


            // Check if user exists with this social ID
            $user = User::where('social_id', $request->social_id)
                       ->where('social_provider', $request->provider)
                       ->first();

            if (!$user) {
                // Check if user exists with this email
                $user = User::where('email', $request->email)->first();

                if ($user) {
                    // Link social account to existing user
                    return $this->error('This email is already associated with another account. Please use a different one.');
                    $user->update([
                        'social_id' => $request->social_id,
                        'social_provider' => $request->provider,
                    ]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'social_id' => $request->social_id,
                        'social_provider' => $request->provider,
                        'email_verified_at' => now(),
                        'password' => Hash::make(uniqid()), // Random password for social users
                    ]);
                    Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
                    $user->syncRoles('customer');
                }
            }

            if ($user->hasAnyRole(['coach', 'nutritionist', 'admin'])) {
                return $this->forbidden('Access denied. Coaches and Nutritionists cannot log in via social accounts.');
            }

            // Create token with Passport
            $tokenResult = $user->createToken($request->device_name);
            $token = $tokenResult->accessToken;

            // Check if onboarding is completed
            $onboardingCompleted = $user->userProfile !== null;

            return $this->success([
                'user' => $this->formatUserData($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token->expires_at,
                'onboarding_completed' => $onboardingCompleted,
            ], 'Social login successful');

        } catch (\Exception $e) {
            return $this->serverError('Social login failed. Please try again.');
        }
    }

    /**
     * Logout user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke current token with Passport
            $request->user()->token()->revoke();

            return $this->success(null, 'Logout successful');

        } catch (\Exception $e) {
            return $this->serverError('Logout failed. Please try again.');
        }
    }

    /**
     * Get authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $onboardingCompleted = $user->userProfile !== null;

            return $this->success([
                'user' => $this->formatUserData($user),
                'onboarding_completed' => $onboardingCompleted,
            ]);

        } catch (\Exception $e) {
            return $this->serverError('Failed to retrieve user data.');
        }
    }

    /**
     * Send password reset link.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            // $validator = Validator::make($request->all(), [
            //     'email' => 'required|email|exists:users,email',
            // ]);

            // if ($validator->fails()) {
            //     return $this->validationError($validator->errors());
            // }

            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return $this->success(null, 'Password reset link sent to your email');
            }

            return $this->error('Failed to send password reset link');

        } catch (\Exception $e) {
            return $this->serverError('Failed to send password reset link.');
        }
    }

    /**
     * Send password reset OTP.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendPasswordOtp(Request $request): JsonResponse
    {
        return $this->sendPasswordOtpInternal($request, false);
    }

    /**
     * Resend password reset OTP.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resendPasswordOtp(Request $request): JsonResponse
    {
        return $this->sendPasswordOtpInternal($request, true);
    }

    private function sendPasswordOtpInternal(Request $request, bool $isResend): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $existing = DB::table('password_reset_otps')
                ->where('email', $request->email)
                ->first();

            if ($existing) {
                $createdAt = Carbon::parse($existing->created_at);
                $cooldownUntil = $createdAt->addSeconds(self::OTP_RESEND_COOLDOWN_SECONDS);

                if ($cooldownUntil->isFuture()) {
                    $secondsLeft = now()->diffInSeconds($cooldownUntil);
                    return $this->error("Please wait {$secondsLeft} seconds before requesting a new code.");
                }
            }

            $otp = $this->generateOtp();

            DB::table('password_reset_otps')->updateOrInsert(
                ['email' => $request->email],
                [
                    'otp_hash' => Hash::make($otp),
                    'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                    'verified_at' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            Mail::raw("Your Fitwnata OTP code is: {$otp}. It expires in " . self::OTP_TTL_MINUTES . " minutes.", function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Your Fitwnata OTP Code');
            });

            $message = $isResend ? 'OTP resent successfully' : 'OTP sent successfully';
            return $this->success(['expires_in' => self::OTP_TTL_MINUTES * 60], $message);
        } catch (\Exception $e) {
            return $this->serverError('Failed to send OTP.');
        }
    }

    private function generateOtp(): string
    {
        $max = (10 ** self::OTP_LENGTH) - 1;
        $otp = (string) random_int(0, $max);
        return str_pad($otp, self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Verify password reset OTP.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPasswordOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|string|min:' . self::OTP_LENGTH,
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $otpRecord = DB::table('password_reset_otps')
                ->where('email', $request->email)
                ->first();

            if (!$otpRecord) {
                return $this->error('OTP not found. Please request a new code.');
            }

            if (Carbon::parse($otpRecord->expires_at)->isPast()) {
                return $this->error('OTP expired. Please request a new code.');
            }

            if (!Hash::check($request->otp, $otpRecord->otp_hash)) {
                return $this->error('Invalid OTP. Please try again.');
            }

            DB::table('password_reset_otps')
                ->where('email', $request->email)
                ->update(['verified_at' => now()]);

            return $this->success(null, 'OTP verified successfully');
        } catch (\Exception $e) {
            return $this->serverError('Failed to verify OTP.');
        }
    }

    /**
     * Reset password using OTP.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPasswordWithOtp(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|string|min:' . self::OTP_LENGTH,
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $otpRecord = DB::table('password_reset_otps')
                ->where('email', $request->email)
                ->first();

            if (!$otpRecord) {
                return $this->error('OTP not found. Please request a new code.');
            }

            if (Carbon::parse($otpRecord->expires_at)->isPast()) {
                return $this->error('OTP expired. Please request a new code.');
            }

            if (!Hash::check($request->otp, $otpRecord->otp_hash)) {
                return $this->error('Invalid OTP. Please try again.');
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->error('User not found.');
            }

            $user->forceFill([
                'password' => Hash::make($request->password),
            ])->save();

            // Revoke all tokens for security with Passport
            $user->tokens()->update(['revoked' => true]);

            DB::table('password_reset_otps')
                ->where('email', $request->email)
                ->delete();

            return $this->success(null, 'Password reset successful');
        } catch (\Exception $e) {
            return $this->serverError('Password reset failed.');
        }
    }

    /**
     * Reset password.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();

                    // Revoke all tokens for security with Passport
                    $user->tokens()->update(['revoked' => true]);
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->success(null, 'Password reset successful');
            }

            return $this->error('Password reset failed');

        } catch (\Exception $e) {
            return $this->serverError('Password reset failed.');
        }
    }

    /**
     * Refresh authentication token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_name' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator->errors());
            }

            $user = $request->user();

            // Revoke current token
            $request->user()->token()->revoke();

            // Create new token with Passport
            $tokenResult = $user->createToken($request->device_name);
            $token = $tokenResult->accessToken;

            return $this->success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token->expires_at,
            ], 'Token refreshed successfully');

        } catch (\Exception $e) {
            return $this->serverError('Token refresh failed.');
        }
    }

    /**
     * Format user data for API response.
     *
     * @param User $user
     * @return array
     */
    private function formatUserData(User $user): array
    {
        // Load the user profile if it exists
        $user->load('userProfile');

        $roles = $user->getRoleNames();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'profile_photo_url' => $user->profile_photo_url ?? null,
            'roles' => $user->getRoleNames(),
            'role' => $roles->first() ?? null,
            'created_at' => $user->created_at,
            'profile' => $user->userProfile ?? null,
        ];
    }

    public function checkOnboarding(Request $request)
    {
        $user = Auth::User();

        $profile = UserProfile::where('user_id', $user->id)->first();

        if($profile)
        {
            $onboarding = true;
        }
        else
        {
            $onboarding = false;
        }

        return response()->json([
            'status' => true,
            'message' => 'Onboarding Status',
            'onboarding' => $onboarding
        ]);
    }
}
