<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
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
        Route::post('/client/store','clientStore')->name('client.store');
        Route::post('/client/edit','clientEdit')->name('client.edit');
        Route::get('/client/delete/{client_id}','clientDelete')->name('client.delete');
        Route::get('/client/delete/{client_id}/{picture_id}','clientDeletePic')->name('client.delete.pic');
    });

    Route::controller(FreelancerController::class)->group(function () {
        Route::get('/freelancer', 'freelancer')->name('freelancer');
    });

    Route::controller(ServiceController::class)->group(function () {
        Route::get('/service', 'service')->name('service');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/order', 'order')->name('order');
    });

    Route::controller(ReportController::class)->group(function () {
        Route::get('/report', 'report')->name('report');
    });
});

require __DIR__.'/auth.php';
