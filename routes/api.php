<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\ForgotPasswordController;




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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('getdata',function(){
    echo "Welcome to Website";
});

Route::post('signup',[AuthController::class,'signup']);

Route::post('login', [AuthController::class,'login']);

Route::post('/upload-images', [ImageUploadController::class, 'uploadImages']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);

Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);

Route::post('resetPassword', [ForgotPasswordController::class, 'resetPassword']);

