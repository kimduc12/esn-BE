<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\BrandController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::BRAND_LIST]], function () {
    Route::get('brands', [BrandController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BRAND_ADD]], function () {
    Route::post('brands', [BrandController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BRAND_DETAIL]], function () {
    Route::get('brands/{id}', [BrandController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BRAND_EDIT]], function () {
    Route::put('brands/{id}', [BrandController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BRAND_DELETE]], function () {
    Route::delete('brands', [BrandController::class, 'destroy']);
});
