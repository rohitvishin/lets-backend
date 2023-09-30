<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\admin\AdminController;
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
    return view('welcome');
});

Route::get('/adminlogin',[AdminController::class,'adminLogin'])->name('adminLogin');

Route::post('/adminLoginPost',[AdminController::class,'adminLoginPost']);

Route::get('/reset-password', [UserController::class, 'resetPasswordLoadPage']);

Route::post('/reset-password', [UserController::class,  'resetPassword']);

Route::get('/forgot-password',function(){
    return view('forgot-password');
})->name('forgot-password');

Route::prefix('admin')->group(function () {

    // Get Routes
    Route::get('/dashboard',[AdminController::class,'adminDashboard'])->name('adminDashboard');
    Route::get('/allUsers',[AdminController::class,'allUsers'])->name('allUsers');
    Route::get('/allPackages',[AdminController::class,'allPackages'])->name('allPackages');
    Route::get('/allSupport',[AdminController::class,'allSupport'])->name('allSupport');
    Route::get('/myAccount',[AdminController::class,'myAccount'])->name('myAccount');
    Route::get('/settings',function(){
        return view('admin.settings');
    })->name('settings');

    Route::post('/updateUserStatus',[AdminController::class,'updateUserStatus']);
    Route::post('/addPackage',[AdminController::class,'addPackage']);
    Route::post('/deletePackage',[AdminController::class,'deletePackage']);
    Route::post('/deleteUser',[AdminController::class,'deleteUser']);

    Route::post('/changePwd',[AdminController::class,'changePwd']);

    Route::get('/adminLogout',[AdminController::class,'adminLogout'])->name('adminLogout');
});