<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\MaintenanceController;
use App\Http\Controllers\API\MemoController;
use App\Http\Controllers\API\StatusController;
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
// Login v1
Route::post('v1/login', [AuthController::class, 'login']);

//API v1
Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {


    // User Routes
    // Get all users
    Route::get('/users', [UserController::class, 'index'])->middleware(['scope:admin']); // Admin
    // create new user
    Route::middleware(['scopes:admin'])->post('/users', [UserController::class, 'store']); // Admin
    // update exsisting user
    Route::put('/users/{user}', [UserController::class, 'update']); // Admin or current user
    // get user by id
    Route::get('/users/{user}', [UserController::class, 'show']); // Admin or current user

    Route::middleware(['scope:admin'])->delete('/users/{user}', [UserController::class, 'destroy']);
    Route::get('/users/verify/email', [UserController::class, 'isEmailisAlreadyRegistered']);




    // Customers routes
    // CRUD Operations
    Route::middleware(['scope:admin,user,read-only'])->get('/customers', [CustomerController::class, 'index']);
    Route::middleware(['scope:admin'])->post('/customers', [CustomerController::class, 'store']);
    Route::middleware(['scope:admin,user,read-only'])->get('/customers/{customer}', [CustomerController::class, 'show']);
    Route::middleware(['scope:admin'])->put('/customers/{customer}', [CustomerController::class, 'update']);
    Route::middleware(['scope:admin'])->delete('/customers/{customer}', [CustomerController::class, 'destroy']);
    // Customer Devices
    Route::middleware(['scope:admin,user,read-only'])->get('/customers/{customer}/devices', [CustomerController::class, 'getDevices']);



    // Devices routes
    // CRUD Operations
    Route::middleware(['scope:admin,user,read-only'])->get('/devices', [DeviceController::class, 'index']);
    Route::middleware(['scope:admin,user'])->post('/devices', [DeviceController::class, 'store']);
    Route::middleware(['scope:admin,user,read-only'])->get('/devices/{device}', [DeviceController::class, 'show']);
    Route::middleware(['scope:admin,user'])->put('/devices/{device}', [DeviceController::class, 'update']);
    Route::middleware(['scope:admin'])->delete('/devices/{device}', [DeviceController::class, 'destroy']);
    // Device Maintenances
    Route::middleware(['scope:admin,user,read-only'])->get('/devices/{device}/maintenances', [DeviceController::class, 'getMaintenances']);


    // Maintenances routes
    // CRUD Operations
    Route::middleware(['scope:admin,user,read-only'])->get('/maintenances', [MaintenanceController::class, 'index']);
    Route::middleware(['scope:admin,user'])->post('/maintenances', [MaintenanceController::class, 'store']);
    Route::middleware(['scope:admin,user,read-only'])->get('/maintenances/{maintenance}', [MaintenanceController::class, 'show']);
    Route::middleware(['scope:admin,user'])->put('/maintenances/{maintenance}', [MaintenanceController::class, 'update']);
    Route::middleware(['scope:admin'])->delete('/maintenances/{maintenance}', [MaintenanceController::class, 'destroy']);
    // Maintenances Memos
    Route::middleware(['scope:admin,user,read-only'])->get('/maintenances/{maintenance}/memos', [MaintenanceController::class, 'getMemos']);


    // Memos routes
    // CRUD Operations
    Route::middleware(['scope:admin,user,read-only'])->get('/memos', [MemoController::class, 'index']);
    Route::middleware(['scope:admin,user'])->post('/memos', [MemoController::class, 'store']);
    Route::middleware(['scope:admin,user,read-only'])->get('/memos/{memo}', [MemoController::class, 'show']);
    Route::middleware(['scope:admin,user'])->put('/memos/{memo}', [MemoController::class, 'update']);
    Route::middleware(['scope:admin'])->delete('/memos/{memo}', [MemoController::class, 'destroy']);

    // Image Routes
    Route::middleware(['scope:admin,user'])->post('/images', [ImageController::class, 'store']);
    Route::middleware(['scope:admin,user'])->post('/images/base64', [ImageController::class, 'storeBase64']);

    // Status Routes
    Route::middleware(['scope:admin,user,read-only'])->get('/statuses', [StatusController::class, 'index']);
});
