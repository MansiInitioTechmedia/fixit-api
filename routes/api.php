<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\ScheduleController;





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

Route::post('/upload-images', [ImageUploadController::class, 'uploadImages']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);

Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);

Route::post('/resetPassword', [ForgotPasswordController::class, 'resetPassword']);



//Vehicle Controllers Routes
Route::prefix('vehicles')->group(function () {
    Route::get('/index', [VehicleController::class, 'index']); // Get all vehicles
    Route::post('/create', [VehicleController::class, 'store']); // Store a new vehicle
    Route::get('show/{vehicle}', [VehicleController::class, 'show']); // Show a single vehicle
    Route::put('update/{vehicle}', [VehicleController::class, 'update']); // Update a vehicle
    Route::delete('destroy/{vehicle}', [VehicleController::class, 'destroy']); // Delete a vehicle
});



Route::prefix('categories')->group(function () {
    Route::get('/index', [CategoryController::class, 'index']); // Get all categories
    Route::post('/create', [CategoryController::class, 'store']); // Store a new category
    Route::get('/show/{category}', [CategoryController::class, 'show']); // Show a single category
    Route::put('/update/{category}', [CategoryController::class, 'update']); // Update a category
    Route::delete('/destroy/{category}', [CategoryController::class, 'destroy']); // Delete a category
});



Route::prefix('schedules')->middleware('auth:sanctum')->group(function () {
    Route::get('/index', [ScheduleController::class, 'index']); // Get all schedules
    Route::post('/create', [ScheduleController::class, 'store']); // Create a new schedule
    
});
