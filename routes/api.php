<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\API\AuthController;
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

Route::middleware('auth:api')->get('/user', [UserController::class, 'index']);


// Route::middleware('auth:api')->get('/customer', [CustomerController::class, 'index']);

Route::group(['prefix' => 'v1','middleware' => 'auth:api'], function () {
    // CRUD Operations
    Route::apiResource('/customer', CustomerController::class);
    // Customer Devices
    Route::get('/customers/{customer}/devices', [CustomerController::class, 'getDevices']);

});

Route::group(['prefix' => 'v1','middleware' => 'auth:api'], function () {
    // CRUD Operations
    Route::apiResource('/devices', DeviceController::class);

});
Route::group(['prefix' => 'v1'], function () {
    Route::post('/login', [AuthController::class, 'login']);
});

