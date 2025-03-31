<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ScheduleController;





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

Route::post('/signup',[AuthController::class,'signup']);

Route::post('/login', [AuthController::class,'login']);

Route::post('logout', [AuthController::class,'logout'])->middleware('auth:sanctum');

Route::post('/upload-images', [ImageUploadController::class, 'uploadImages']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);

Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);

Route::post('/resetPassword', [ForgotPasswordController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->post('/changePassword', [ForgotPasswordController::class, 'changePassword']);




// Vehicle Controllers Routes
Route::prefix('vehicles')->group(function () {
    Route::get('/index', [VehicleController::class, 'index']); 
    Route::post('/create', [VehicleController::class, 'store']); 
    Route::get('/show/{vehicle}', [VehicleController::class, 'show']); 
    Route::put('/update/{vehicle}', [VehicleController::class, 'update']); 
    Route::delete('/destroy/{vehicle}', [VehicleController::class, 'destroy']); 
});


// categories Controllers Routes
Route::prefix('categories')->group(function () {
    Route::get('/index', [CategoryController::class, 'index']); 
    Route::post('/create', [CategoryController::class, 'store']); 
    Route::get('/show/{category}', [CategoryController::class, 'show']); 
    Route::put('/update/{category}', [CategoryController::class, 'update']); 
    Route::delete('/destroy/{category}', [CategoryController::class, 'destroy']); 
});



// schedules Controllers Routes
Route::prefix('schedules')->group(function () {
    Route::get('/index', [ScheduleController::class, 'index']); 
    Route::post('/create', [ScheduleController::class, 'store']); 
    Route::get('/show/{id}', [ScheduleController::class, 'show']); 
});



