<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\GiftController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::GIFT_LIST]], function () {
    Route::get('gifts', [GiftController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::GIFT_ADD]], function () {
    Route::post('gifts', [GiftController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::GIFT_DETAIL]], function () {
    Route::get('gifts/{id}', [GiftController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::GIFT_EDIT]], function () {
    Route::put('gifts/{id}', [GiftController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::GIFT_DELETE]], function () {
    Route::delete('gifts', [GiftController::class, 'destroy']);
});
