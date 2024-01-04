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

        $user = user::whereEmail($request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Invalid Credentials',
            ], 422);
        }

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
