<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PlansController;
use App\Http\Controllers\API\UserController;

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

Route::group(['middleware' => 'auth:sanctum'], function(){

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/getUser', [UserController::class, 'index']);

    Route::post('/updateUser', [UserController::class, 'update']);

    Route::post('/resetPassword', [UserController::class, 'changePassword']);

    Route::get('/getPackages', [PlansController::class, 'index']);

});

Route::post('/signup', [AuthController::class, 'sign_up']);

Route::post('/login', [AuthController::class, 'login']);

?>