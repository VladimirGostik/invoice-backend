<?php

namespace App\Http\Controllers;

use App\Enums\UserStateEnum;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response as ScribeResponse;
use Knuckles\Scribe\Attributes\Unauthenticated;

#[Group('Authentication')]

class AuthController extends Controller implements HasMiddleware
{
    // New laravel 11 way of defining middleware in controllers
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'auth:api', except: ['login', 'forgotPassword', 'resetPassword']),
        ];
    }

    /**
     * Login
     *
     * Get a JWT via given credentials.
     *
     */
    #[Unauthenticated]
    #[BodyParam('email', 'string', 'The email of the user.', required: true, example: 'admin@example.com')]
    #[BodyParam('password', 'string', 'The password of the user.', required: true, example: 'password')]
    #[ScribeResponse(content: [
        'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE2OTU3MTIwNTEsImV4cCI6MTY5NTcxNTY1MSwibmJmIjoxNjk1NzEyMDUxLCJqdGkiOiJJeWVpVmFHSWoyZFZyRG5YIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjciLCJlbnRpdHlfaWQiOm51bGx9.f-mRC92U2zhjPI1pUCFRfFYnwQI3jgyLMCnznIHoxk4',
        'token_type' => 'bearer',
        'expires_in' => '3600',
    ], status: 200, description: 'success')]
    #[ScribeResponse(content: [
        'message' => 'messages.unauthorized_access',
    ], status: 401, description: 'unauthorized')]
    public function login(LoginRequest $request): TokenResource|JsonResponse
    {
        $credentials = array_merge(
            $request->only(['email', 'password']),
            ['state' => UserStateEnum::ACTIVE]
        );

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['message' => __('messages.unauthorized_access')], 401);
        }

        return new TokenResource([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Forgot password
     *
     * Send a password reset link to user's email
     */
    #[Unauthenticated]
    #[BodyParam('email', 'string', 'The email of the user.', required: true, example: 'admin@example.com')]
    #[ScribeResponse(content: ['status' => 'Status message'], status: 200, description: 'success')]
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json(['status' => __($status)]);
    }

    /**
     * Reset password
     *
     * Reset user's password
     */
    #[Unauthenticated]
    #[BodyParam('token', 'string', 'The password reset token.', required: true, example: 'dd37ed6ef02e80ea8ced619f8bfbd5604568404a58b77cc0572575ccf0c8a159')]
    #[BodyParam('email', 'string', 'The email of the user.')]
    #[BodyParam('password', 'string', 'The new password.', required: true, example: 'e@#L_EW>Vn')]
    #[BodyParam('password_confirmation', 'string', 'Confirm the new password.', required: true, example: 'e@#L_EW>Vn')]
    #[ScribeResponse(content: ['status' => 'Status message'], status: 200, description: 'success')]
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json(['status' => __($status)]);
    }

    /**
     * Get user
     *
     * Get the authenticated User.
     */
    #[ScribeResponse(content: [
        'id' => 1,
        'email' => 'admin@example.com',
        'first_name' => 'Test',
        'last_name' => 'Admin',
        'phone' => '+1.646.261.0356',
        'email_verified_at' => '2023-09-21T10 =>27 =>09.000000Z',
        'state' => 'ACTIVE',
        'created_at' => '2023-09-21T10 =>27 =>09.000000Z',
        'updated_at' => '2023-09-21T10 =>27 =>09.000000Z',
    ], status: 200, description: 'success')]
    public function user(): UserResource
    {
      //  auth()->user()->load('selectedEntity');
        return UserResource::make(auth()->user());
    }

    /**
     * Logout
     *
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request): Response
    {
        auth()->logout(true);

        return response()->noContent();
    }

    /**
     * Refresh token
     *
     * Refresh a token.
     */
    #[ScribeResponse(content: [
        'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE2OTU3MTIwNTEsImV4cCI6MTY5NTcxNTY1MSwibmJmIjoxNjk1NzEyMDUxLCJqdGkiOiJJeWVpVmFHSWoyZFZyRG5YIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjciLCJlbnRpdHlfaWQiOm51bGx9.f-mRC92U2zhjPI1pUCFRfFYnwQI3jgyLMCnznIHoxk4',
        'token_type' => 'bearer',
        'expires_in' => '3600',
    ], status: 200, description: 'success')]
    public function refresh(Request $request): TokenResource
    {
        $token = auth()->login(auth()->user());

        return new TokenResource([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}