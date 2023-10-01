<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PlansController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\LetsController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\TransactionController;

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

Route::get('/test', function () {
    return "Test";
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/getUser', [UserController::class, 'index']);

    //Without User's Email and Phone
    Route::post('/updateUser', [UserController::class, 'update']);

    Route::post('/updateUserLocation', [UserController::class, 'update_location_api']);

    Route::post('/updateUserFilter', [UserController::class, 'update_filter_api']);

    Route::post('/resetPassword', [UserController::class, 'changePassword']);

    Route::get('/getPackages', [PlansController::class, 'index']);

    Route::post('/buySubscriptions', [SubscriptionController::class, 'store']);

    Route::post('/verifyEmail', [AuthController::class, 'verify_email']);

    Route::post('/letsCreator', [LetsController::class, 'letsCreator']);

    Route::post('/letsAcceptor', [LetsController::class, 'letsAcceptor']);

    Route::get('/getLets', [LetsController::class, 'getLetsDetails']);
    Route::get('/getMatchLocation', [LetsController::class, 'getMatchLocation']);
    Route::post('/updateMatchDetails', [LetsController::class, 'updateMatchDetails']);

    Route::get('/getSubscriptions', [SubscriptionController::class, 'index']);

    Route::get('/getLetsRequest', [LetsController::class, 'getLetsDetailRequests']);

    Route::post('/report', [ReportController::class, 'store']);

    Route::get('/getReports', [ReportController::class, 'show']);

    Route::post('/updateUserFilter', [UserController::class, 'update_filter_api']);

    Route::post('/updateUserLocation', [UserController::class, 'update_location_api']);

    Route::post('/getUserDetails', [UserController::class, 'getUserDetails']);

    Route::post('/forgotPassword', [UserController::class, 'forgotPassword']);

    Route::post('/getOrderId', [TransactionController::class, 'getOrderId']);

    Route::post('/updatePaymentStatus', [TransactionController::class, 'updatePaymentStatus']);
});

Route::post('/signup', [AuthController::class, 'sign_up']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/verifyEmail', [AuthController::class, 'verify_email']);

Route::post('/forgot-password', [UserController::class, 'forgotPassword']);

Route::post('reset', [UserController::class, 'reset']);
