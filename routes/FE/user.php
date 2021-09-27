<?php
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LoginController;
use Illuminate\Support\Facades\Route;


Route::post('login', [LoginController::class, 'login']);
Route::post('login-by-email', [LoginController::class, 'loginByEmail']);
Route::post('login-by-username', [LoginController::class, 'loginByUsername']);
Route::post('login-by-facebook', [LoginController::class, 'loginByFacebook']);
Route::post('login-by-google', [LoginController::class, 'loginByGoogle']);
Route::post('login-by-zalo', [LoginController::class, 'loginByZalo']);
Route::post('register', [UserController::class, 'register']);
Route::post('forget-password', [UserController::class, 'forgetPassword']);
Route::post('users/check-email', [UserController::class, 'checkEmail']);
Route::post('users/check-phone', [UserController::class, 'checkPhone']);

Route::post('reset-password', [UserController::class, 'resetPassword'])->name('password.update');

Route::group(['middleware' => ['auth:api']], function(){
    Route::get('users/get-my-info', [UserController::class, 'getMyInfo']);
    Route::put('users/update-my-info', [UserController::class, 'updateMyInfo']);
    Route::get('users/history-exchange-gifts', [UserController::class, 'getHistoryExchangedGifts']);
});
