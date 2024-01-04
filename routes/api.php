<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\ChatControllerTest;
use App\Http\Controllers\API\ChatMessageController;
use App\Http\Controllers\API\FreelancerController;
use App\Http\Controllers\API\MidtransController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\SavedServiceController;
use App\Http\Controllers\API\SellerController;
use App\Http\Controllers\API\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
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

// Broadcast::routes(['middleware' => ['auth:sanctum']]);

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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/getRequest', [ProfileController::class, 'getReq']);
    Route::get('/getUserType', [ProfileController::class, 'getUserType']);
    Route::post('/sendIssue', [ProfileController::class, 'sendIssue']);
    Route::post('/packageActivation', [ServiceController::class, 'packageActivation']);
    Route::post('/changeProfilePicture', [ProfileController::class, 'changeProfilePicture']);

    Route::get('/getServiceFreelancer', [ServiceController::class, 'getServiceFreelancer']);
    Route::get('/getRecommendation', [ServiceController::class, 'getRecommendation']);

    Route::post('/freelancerActivation', [FreelancerController::class, 'freelancerActivation']);

    Route::post('/chat/create', [ChatController::class, 'createChat']);
    Route::post('/chat/sendMessage', [ChatController::class, 'sendMessage']);
    Route::get('/chat/getAllChat', [ChatController::class, 'getAllChat']);
    Route::get('/chat/getAllMessage', [ChatController::class, 'getAllMessage']);

    Route::get('/getProfileImage', [ChatController::class, 'getProfileImage']);

    Route::apiResource('savedService', SavedServiceController::class);
    route::get('/getDisplayBySubCategoryIdAuth/{subcategory_id}', [ServiceController::class, 'getDisplayBySubCategoryIdAuth']);

    Route::post('/payments/midtrans', [MidtransController::class, 'create']);
});

Route::get('/getPopularService', [ServiceController::class, 'getPopularService']);
Route::get('/getAllCategory', [ServiceController::class, 'getAllCategory']);
Route::get('/getAllSubCategory/{category_id}', [ServiceController::class, 'getAllSubCategory']);
route::get('/getDisplayBySubCategoryIdNoAuth/{subcategory_id}', [ServiceController::class, 'getDisplayBySubCategoryIdNoAuth']);

Route::get('/getLowestPrice/{service_id}', [ServiceController::class, 'getLowestPrice']);
Route::get('/getRating/{service_id}', [ServiceController::class, 'getRating']);
Route::get('/getAImage/{service_id}', [ServiceController::class, 'getAImage']);
Route::get('/getServiceImage/{service_id}', [ServiceController::class, 'getServiceImage']);
Route::get('/getServicePackage/{service_id}', [ServiceController::class, 'getServicePackage']);

Route::post('/payments', [PaymentController::class, 'create']);

Route::post('/payments/webhook/xendit', [PaymentController::class, 'webhook']);
Route::post('/payout', [PaymentController::class, 'payout']);
