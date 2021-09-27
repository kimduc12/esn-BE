<?php

use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\BannerController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::BANNER_LIST]], function () {
    Route::get('banners', [BannerController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BANNER_ADD]], function () {
    Route::post('banners', [BannerController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BANNER_DETAIL]], function () {
    Route::get('banners/{id}', [BannerController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BANNER_EDIT]], function () {
    Route::put('banners/{id}', [BannerController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BANNER_DELETE]], function () {
    Route::delete('banners', [BannerController::class, 'destroy']);
});


