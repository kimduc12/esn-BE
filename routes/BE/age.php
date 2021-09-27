<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\AgeController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::AGE_LIST]], function () {
    Route::get('ages', [AgeController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::AGE_ADD]], function () {
    Route::post('ages', [AgeController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::AGE_DETAIL]], function () {
    Route::get('ages/{id}', [AgeController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::AGE_EDIT]], function () {
    Route::put('ages/{id}', [AgeController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::AGE_DELETE]], function () {
    Route::delete('ages', [AgeController::class, 'destroy']);
});
