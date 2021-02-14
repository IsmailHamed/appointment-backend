<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Expert\ExpertBookingController;
use App\Http\Controllers\API\Expert\ExpertController;
use App\Http\Controllers\API\Expert\ExpertWorkHourController;
use App\Http\Controllers\API\Me\MeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('login-as-guest', [AuthController::class, 'loginAsGuest']);
    Route::put('update', [AuthController::class, 'update']);
    Route::get('refresh', [AuthController::class, 'refresh']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::post('request-forget-password', [AuthController::class, 'requestForgetPassword']);
    Route::post('forget-password', [AuthController::class, 'forgetPassword']);
    Route::post('request-email-validation', [AuthController::class, 'requestEmailValidation']);
    Route::post('validate-email', [AuthController::class, 'validateEmail']);
    Route::get('me', [MeController::class, 'me']);
});
Route::resource('experts', ExpertController::class);
Route::resource('experts.bookings', ExpertBookingController::class);
Route::resource('experts.workHours', ExpertWorkHourController::class);
Route::post('experts/{expert}/get-availability-time', [ExpertController::class, 'getAvailabilityTime']);


