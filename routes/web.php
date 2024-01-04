<?php

use App\Events\HelloEvent;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/send-event', function () {
    broadcast(new \App\Events\HelloEvent());
});

Route::get('/dashboard', function () {
    return view('admin.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile/store', [ProfileController::class, 'profileStore'])->name('profile.store');
    Route::get('/change/password', [ProfileController::class, 'changePassword'])->name('changePassword');
    Route::post('/update/password', [ProfileController::class, 'updatePassword'])->name('update.password');

    Route::controller(ClientController::class)->group(function () {
        Route::get('/client', 'client')->name('client');
        Route::post('/client/store', 'clientStore')->name('client.store');
        Route::post('/client/edit', 'clientEdit')->name('client.edit');
        Route::get('/client/delete/{client_id}/{user_id}', 'clientDelete')->name('client.delete');
        Route::get('/client/delete/{client_id}/{user_id}/{picture_id}', 'clientDeletePic')->name('client.delete.pic');
    });

    Route::controller(FreelancerController::class)->group(function () {
        Route::get('/freelancer', 'freelancer')->name('freelancer');
        Route::post('/freelancer/store', 'freelancerStore')->name('freelancer.store');
        Route::post('/freelancer/edit', 'freelancerEdit')->name('freelancer.edit');
        Route::get('/freelancer/delete/{freelancer_id}/{user_id}', 'freelancerDelete')->name('freelancer.delete');
        Route::get('/freelancer/delete/{freelancer_id}/{user_id}/{picture_id}', 'freelancerDeletePic')->name('freelancer.delete.pic');
        Route::get('/freelancer/profile/{freelancer_id}', 'freelancerProfile')->name('freelancer.profile');

        Route::get('/freelancer/request', 'freelancerRequest')->name('freelancer.request');
        Route::get('/freelancer/request/{freelancer_id}', 'freelancerRequestDetails')->name('freelancer.request.details');
        Route::post('/freelancer/request/{user_id}/approve/{freelancer_id}', 'requestApprove')->name('freelancer.request.approve');
        Route::post('/freelancer/request/{user_id}/reject/{freelancer_id}', 'requestReject')->name('freelancer.request.reject');
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::get('/service', 'service')->name('service');
        Route::get('/service/request', 'serviceRequest')->name('service.request');
        Route::get('/service/request/{service_id}', 'serviceRequestDetails')->name('service.request.details');
        Route::post('/service/request/approve/{service_id}', 'requestApprove')->name('service.request.approve');
        Route::post('/service/request/reject/{service_id}', 'requestReject')->name('service.request.reject');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/order', 'order')->name('order');
    });

    Route::controller(ReportController::class)->group(function () {
        Route::get('/report', 'report')->name('report');
        Route::put('/report/{report_id}/{status}','changeReportStatus')->name('report.status');
    });
});

require __DIR__ . '/auth.php';
