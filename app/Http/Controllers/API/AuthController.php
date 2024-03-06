<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\RegisterClientRequest;
use App\Http\Requests\API\RegisterFreelancerRequest;
use App\Http\Requests\API\RegisterRequest;
use App\Models\client;
use App\Models\freelancer;
use App\Models\picture;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ThrottlesAttempts;
    public function register(RegisterRequest $request)
    {
        $request->validated();
        $location = $request->location ?? '';
        $currentTimestamp = Carbon::now();

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'location' => $location,
            'picture_id' => null,
            'status' => 'active',
            'email_verified_at' => null,
            'remember_token' => null,
            'profile_type' => 'client',
            'last_login' => $currentTimestamp,
        ];

        $user = user::create($userData);

        $token = $user->createToken('freelancer-app')->plainTextToken;

        client::create([
            'user_id' => $user->user_id,
            'orders_made' => 0,
            'total_spent' => 0,
        ]);

        return response([
            'user' => $user,
            'token' => $token
        ], 201);
    }
    public function login(LoginRequest $request)
    {
        $request->validated();

        if ($this->hasTooManyAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $user = user::whereEmail($request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->incrementAttempts($request);
            return response([
                'message' => 'Invalid Credentials',
            ], 422);
        }

        $this->clearAttempts($request);
        $token = $user->createToken('freelancer-app')->plainTextToken;
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
