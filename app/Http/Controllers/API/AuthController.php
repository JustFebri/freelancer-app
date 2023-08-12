<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RegisterClientRequest;
use App\Http\Requests\API\RegisterFreelancerRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\client;
use App\Models\freelancer;
use App\Models\picture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registerClient(RegisterClientRequest $request)
    {
        $request->validated();

        $location = $request->location ?? '';

        $clientData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'location' => $location,
            'picture_id' => null,
            'status' =>  'active',
            'email_verified_at' => null,
            'remember_token' => null
        ];

        $client = client::create($clientData);
        $token = $client->createToken('freelancer-app')->plainTextToken;

        return response([
            'client' => $client,
            'token' => $token
        ], 201);
    }

    public function loginClient(LoginRequest $request)
    {
        $request->validated();

        $client = client::whereEmail($request->email)->first();
        if (!$client || !Hash::check($request->password, $client->password)) {
            return response([
                'message' => 'Invalid Credentials',
            ], 422);
        }

        $token = $client->createToken('freelancer-app')->plainTextToken;

        return response([
            'client' => $client,
            'token' => $token
        ], 201);
    }

    // public function registerFreelancer(RegisterFreelancerRequest $request)
    // {
    //     $request->validated();

    //     $location = $request->location ?? '';

    //     $freelancerData = [
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'location' => $location,
    //         'picture_id' => $request->picture,
    //         'status' => $request->status,
    //         'information' => $request->information,
    //         'identity_number' => $request->identity_number
    //     ];

    //     // $client = client::create($freelancerData);
    //     // $token = $client->createToken('freelancer-app')->plainTextToken;

    //     return response([
    //         'client' => $freelancerData,
    //     ], 201);
    // }

    // public function loginFreelancer(LoginRequest $request)
    // {
    //     $request->validated();

    //     $freelancer = freelancer::whereEmail($request->email)->first();
    //     if (!$freelancer || !Hash::check($request->password, $freelancer->password)) {
    //         return response([
    //             'message' => 'Invalid Credentials',
    //         ], 422);
    //     }

    //     $token = $freelancer->createToken('freelancer-app')->plainTextToken;

    //     return response([
    //         'freelancer' => $freelancer,
    //         'token' => $token
    //     ], 201);
    // }
}
