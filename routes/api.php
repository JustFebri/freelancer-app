<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FreelancerController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\SellerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return response([
        'message' => 'Api is working'
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/getRequest', [ProfileController::class, 'getReq'])->middleware('auth:sanctum');
Route::get('/getUserType', [ProfileController::class, 'getUserType'])->middleware('auth:sanctum');
Route::post('/freelancerActivation', [FreelancerController::class, 'freelancerActivation'])->middleware('auth:sanctum');
Route::post('/payments', [PaymentController::class, 'create']);
Route::post('/payments/webhook/xendit', [PaymentController::class, 'webhook']);
Route::post('/payout', [PaymentController::class, 'payout']);
