<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ChangeTypeController;
use App\Http\Requests\API\UpdateProfileRequest;
use App\Models\freelancer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function getReq()
    {
        $currentUserId = auth()->id();
        $record = freelancer::where('user_id', '=', $currentUserId)->first();
        Log::info($record);

        if ($record) {
            return response([
                'message' => 'Record found',
            ], 201);
        } else {
            return response([
                'message' => 'No record found',
            ], 404); // Or any appropriate status code like 404 Not Found
        }
    }

    public function getUserType()
    {
        $currentUserId = auth()->id();
        $record = User::where('user_id', '=', $currentUserId)->first();

        if ($record->profile_type == 'freelancer') {
            return response([
                'message' => 'User is freelancer',
            ], 201);
        } else {
            return response([
                'message' => 'User is not freelancer',
            ], 404); // Or any appropriate status code like 404 Not Found
        }
    }
}
