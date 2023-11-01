<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\freelancer;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    Public function addService(Request $request){
        $currentUserId = auth()->id();
        $record = freelancer::where('user_id', '=', $currentUserId)->first();
        
    }
}
