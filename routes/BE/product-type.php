<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\ProductTypeController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_TYPE_LIST]], function () {
    Route::get('product-types', [ProductTypeController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_TYPE_ADD]], function () {
    Route::post('product-types', [ProductTypeController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_TYPE_DETAIL]], function () {
    Route::get('product-types/{id}', [ProductTypeController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_TYPE_EDIT]], function () {
    Route::put('product-types/{id}', [ProductTypeController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_TYPE_DELETE]], function () {
    Route::delete('product-types', [ProductTypeController::class, 'destroy']);
});
