<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\UserController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::USER_LIST]], function () {
    Route::get('users', [UserController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::USER_ADD]], function () {
    Route::post('users', [UserController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::USER_DETAIL]], function () {
    Route::get('users/{id}', [UserController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::USER_EDIT]], function () {
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::get('users/{id}/lock', [UserController::class, 'lock']);
    Route::get('users/{id}/unlock', [UserController::class, 'unlock']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::USER_DELETE]], function () {
    Route::delete('users', [UserController::class, 'destroy']);
});
