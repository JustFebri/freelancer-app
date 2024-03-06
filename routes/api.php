<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\ChatControllerTest;
use App\Http\Controllers\API\ChatMessageController;
use App\Http\Controllers\API\ClientController;
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
    Route::get('/listTicket', [ProfileController::class, 'ticketList']);

    Route::post('/changeProfilePicture', [ProfileController::class, 'changeProfilePicture']);
    Route::post('/changeUserData', [ProfileController::class, 'changeUserData']);
    Route::post('/changePassword', [ProfileController::class, 'changePassword']);
    Route::get('/download-file/{orderId}', [ClientController::class, 'downloadFile']);

    Route::get('/getServiceFreelancer', [ServiceController::class, 'getServiceFreelancer']);
    Route::get('/getRecommendation', [ServiceController::class, 'getRecommendation']);

    Route::post('/freelancerActivation', [FreelancerController::class, 'freelancerActivation']);
    Route::get('/freelancer/getDropdownItem', [FreelancerController::class, 'getDropdownItem']);
    Route::post('/freelancer/createCustomOrder', [FreelancerController::class, 'createCustomOrder']);
    Route::post('/freelancer/setCustomOrderStatus', [FreelancerController::class, 'setCustomOrderStatus']);
    Route::post('/freelancer/addPortfolio', [FreelancerController::class, 'addPortfolio']);
    Route::post('/freelancer/updateSellerProfile', [FreelancerController::class, 'updateSellerProfile']);
    Route::post('/freelancer/updatePortfolio', [FreelancerController::class, 'updatePortfolio']);
    Route::post('/freelancer/orderConfirmation', [FreelancerController::class, 'orderConfirmation']);
    Route::post('/freelancer/deliver', [FreelancerController::class, 'deliverNow']);

    Route::post('/chat/create', [ChatController::class, 'createChat']);
    Route::post('/chat/sendMessage', [ChatController::class, 'sendMessage']);
    Route::get('/chat/getAllChat', [ChatController::class, 'getAllChat']);
    Route::get('/chat/getAllMessage', [ChatController::class, 'getAllMessage']);

    Route::get('/getProfileImage', [ChatController::class, 'getProfileImage']);
    Route::get('/getBalance', [ProfileController::class, 'getBalance']);

    Route::apiResource('savedService', SavedServiceController::class);
    route::get('/getDisplayBySubCategoryIdAuth/{subcategory_id}', [ServiceController::class, 'getDisplayBySubCategoryIdAuth']);
    Route::get('/getAllOrders/{status}', [ClientController::class, 'getAllOrders']);
    Route::post('completeOrder/{order_id}',[ClientController::class, 'completeOrder']);
    Route::post('sendReview/',[ClientController::class, 'sendReview']);

    Route::post('/midtrans/payment', [MidtransController::class, 'create']);
    Route::post('/balance/payment', [MidtransController::class, 'paymethodBalance']);
    Route::post('/midtrans/payment/custom_order', [MidtransController::class, 'createCustom']);
    Route::post('midtrans/payment/cancel/{order_id}',[MidtransController::class, 'cancel']);
    Route::post('midtrans/payment/refund/{order_id}',[MidtransController::class, 'refund']);

    Route::post('/packageActivation', [FreelancerController::class, 'packageActivation']);
    Route::post('/updateService', [FreelancerController::class, 'updateService']);
    Route::get('/getServiceData/{serviceId}',[FreelancerController::class, 'getServiceData']);
    Route::delete('/deleteSellerService/{serviceId}',[FreelancerController::class, 'deleteSellerService']);

    Route::get('/getHeader',[FreelancerController::class, 'getHeader']);
    Route::get('/getAbout',[FreelancerController::class, 'getAbout']);
    Route::get('/getServices',[FreelancerController::class, 'getServices']);
    Route::get('/getPortfolio',[FreelancerController::class, 'getPortfolio']);
    Route::get('/getPortfolio/{portfolio_id}',[FreelancerController::class, 'getPortfolioById']);
    Route::get('/freelancer/getAllOrder/{status}',[FreelancerController::class, 'getAllOrder']);
    Route::delete('/deletePortfolio/{portfolio_id}',[FreelancerController::class, 'deletePortfolio']);
});

Route::get('/getPopularService', [ServiceController::class, 'getPopularService']);
Route::get('/getAllCategory', [ServiceController::class, 'getAllCategory']);
Route::get('/getAllSubCategory/{category_id}', [ServiceController::class, 'getAllSubCategory']);
route::get('/getDisplayBySubCategoryIdNoAuth/{subcategory_id}', [ServiceController::class, 'getDisplayBySubCategoryIdNoAuth']);
Route::get('/getReview/{service_id}', [ClientController::class, 'getReview']);

Route::get('/getLowestPrice/{service_id}', [ServiceController::class, 'getLowestPrice']);
Route::get('/getRating/{service_id}', [ServiceController::class, 'getRating']);
Route::get('/getAImage/{service_id}', [ServiceController::class, 'getAImage']);
Route::get('/getServiceImage/{service_id}', [ServiceController::class, 'getServiceImage']);
Route::get('/getServicePackage/{service_id}', [ServiceController::class, 'getServicePackage']);
Route::get('/fetchDataSeller/{freelancer_id}', [ClientController::class, 'fetchDataSeller']);

Route::post('/midtrans/payment/webhook', [MidtransController::class, 'webhook']);