<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ForgotPasswordRequest;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\RegisterClientRequest;
use App\Http\Requests\API\RegisterFreelancerRequest;
use App\Http\Requests\API\RegisterRequest;
use App\Http\Requests\API\ResetPasswordRequest;
use App\Http\Requests\API\TokenRequest;
use App\Mail\ResetPassword;
use App\Models\client;
use App\Models\forgot_password;
use App\Models\freelancer;
use App\Models\order;
use App\Models\picture;
use App\Models\user;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ThrottlesAttempts;
    public function register(RegisterRequest $request)
    {
        $currentTimestamp = Carbon::now();

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'picture_id' => null,
            'status' => 'active',
            'email_verified_at' => null,
            'remember_token' => null,
            'profile_type' => 'client',
            'last_login' => $currentTimestamp,
        ];

        $user = user::create($userData);

        client::create([
            'user_id' => $user->user_id,
            'orders_made' => 0,
            'total_spent' => 0,
        ]);

        // $token = $user->createToken('authToken')->plainTextToken;

        $user->sendEmailVerificationNotification();

        return response([
            'user' => $user,
            'message' => 'User Registered. Please check your email to activate your account',
            // 'token' => $token
        ], 201);
    }
    public function login(LoginRequest $request)
    {
        if ($this->hasTooManyAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $user = user::whereEmail($request->email)->first();
        if ($user && $user->status == 'closed') {
            return response([
                'message' => 'Cannot log in. The account is closed.',
            ], 422);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->incrementAttempts($request);
            return response([
                'message' => 'Invalid Credentials',
            ], 422);
        }

        if (!$user->hasVerifiedEmail()) {
            return response([
                'message' => 'Your email address is not verified. Please verify your email to proceed.'
            ], 403);
        }

        $this->clearAttempts($request);
        $token = $user->createToken('authToken')->plainTextToken;
        $currentTimestamp = Carbon::now();
        $user->last_login = $currentTimestamp;
        $user->save();

        $picture = picture::where('picture_id', '=', $user->picture_id)->first();

        return response([
            'user' => $user,
            'picture' => $picture ? $picture->picasset : null,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'User successfully logged out',
        ], 200);
    }

    public function closeAccount(Request $request)
    {
        $currentUserId = auth()->id();

        $freelancer = freelancer::where('user_id', $currentUserId)->first();
        if ($freelancer) {
            $order = order::where('freelancer_id', $freelancer->freelancer_id)
                ->whereNotIn('order_status', ['completed', 'pending', 'awaiting payment', 'cancelled'])
                ->get();

            if (!$order->isEmpty()) {
                return response()->json([
                    'message' => 'Cannot deactivate account. There are still active orders.',
                ], 400);
            }
        }

        $user = user::find($currentUserId);
        $user->status = 'closed';
        $user->save();

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Account Closed',
        ], 200);
    }

    public function forgot(ForgotPasswordRequest $request)
    {
        $user = user::where('email', $request->email)->first();

        if ($user->status == 'closed') {
            return response([
                'message' => 'Cannot reset password. The account is closed.',
            ], 422);
        }

        if (!$user || !$user->email) {
            return response([
                'message' => 'Incorret email address provided',
            ], 404);
        }

        if (!$user->hasVerifiedEmail()) {
            return response([
                'message' => 'Your email address is not verified. Please verify your email to proceed.'
            ], 403);
        }

        $resetPassword = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        if (!$userPassReset = forgot_password::where('email', $user->email)->first()) {
            forgot_password::create([
                'email' => $user->email,
                'token' => $resetPassword,
            ]);
        } else {
            $userPassReset->update([
                'email' => $user->email,
                'token' => $resetPassword,
            ]);
        }

        Mail::to($user->email)->send(new ResetPassword(
            $resetPassword,
        ));

        return response([
            'message' => 'A Code has been sent to your email address',
        ], 200);
    }

    public function checkCode(TokenRequest $request)
    {
        $attributes = $request->validated();

        $user = user::where('email', $attributes['email'])->first();
        if (!$user) {
            return response([
                'message' => 'Incorret email address provided',
            ], 404);
        }

        $resetRequest = forgot_password::where('email', $request->email)->first();
        if (!$resetRequest || $resetRequest->token != $request->token) {
            return response([
                'message' => 'Token mismatch',
            ], 400);
        }

        return response([
            'message' => 'Code Verificated',
        ], 200);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $attributes = $request->validated();

        $user = user::where('email', $attributes['email'])->first();
        $user->password = Hash::make($request->password);
        $user->save();

        $user->tokens()->delete();
        $resetRequest = forgot_password::where('email', $request->email)->first();
        $resetRequest->delete();

        $token = $user->createToken('authToken')->plainTextToken;

        $picture = picture::where('picture_id', '=', $user->picture_id)->first();
        return response([
            'user' => $user,
            'picture' => $picture ? $picture->piclink : null,
            'token' => $token,
            'message' => 'Password Reset Sucess',
        ], 200);
    }

    public function verify($id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response([
                'status' => false,
                'message' => 'Verifying email fails',
            ], 400);
        }

        $user = user::find($id);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return view('layouts.verification');
    }

    public function notice()
    {
        return response([
            'status' => false,
            'message' => 'Your email address is not verified. Please verify your email to proceed.',
        ], 400);
    }

    public function resend($email)
    {
        $user = user::where('email', $email)->first();
        if ($user) {
            $user->sendEmailVerificationNotification();
            return response()->json([
                'status' => true,
                'message' => 'Please check your email to activate your account',
            ]);
        }
    }
}

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ThrottlesAttempts
{
    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param Request $request
     * @return bool
     */
    protected function hasTooManyAttempts(Request $request): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param Request $request
     * @return void
     */
    protected function incrementAttempts(Request $request): void
    {
        $this->limiter()->hit(
            $this->throttleKey($request),
            $this->decayMinutes() * 60
        );
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    protected function sendLockoutResponse(Request $request): void
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            $this->throttleKeyName() => [Lang::get('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param Request $request
     * @return void
     */
    protected function clearAttempts(Request $request): void
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @param Request $request
     * @return void
     */
    protected function fireLockoutEvent(Request $request): void
    {
        event(new Lockout($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param Request $request
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input($this->throttleKeyName())) . '|' . $request->ip();
    }

    /**
     * Get the rate limiter instance.
     *
     * @return RateLimiter
     */
    protected function limiter(): RateLimiter
    {
        return app(RateLimiter::class);
    }

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */
    public function maxAttempts(): int
    {
        return property_exists($this, 'maxAttempts')
            ? $this->maxAttempts
            : 5;
    }

    /**
     * Get the number of minutes to throttle for.
     *
     * @return int
     */
    public function decayMinutes(): int
    {
        return property_exists($this, 'decayMinutes')
            ? $this->decayMinutes
            : 1;
    }

    /**
     * Get the key used to throttle request
     *
     * @return string
     */
    public function throttleKeyName(): string
    {
        return property_exists($this, 'throttleKeyName')
            ? $this->throttleKeyName
            : 'email';
    }
}
