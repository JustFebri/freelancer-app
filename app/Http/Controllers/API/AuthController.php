<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RegisterClientRequest;
use App\Models\client;
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
        $token = $client->createToken('forumapp')->plainTextToken;

        return response([
            'client' => $client,
            'token' => $token
        ], 201);
    }

    public function registerFreelancer()
    {
    }
}
