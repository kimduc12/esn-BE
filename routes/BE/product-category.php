<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\ProductCategoryController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_CATEGORY_LIST]], function () {
    Route::get('product-categories', [ProductCategoryController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_CATEGORY_ADD]], function () {
    Route::post('product-categories', [ProductCategoryController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_CATEGORY_DETAIL]], function () {
    Route::get('product-categories/{id}', [ProductCategoryController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_CATEGORY_EDIT]], function () {
    Route::put('product-categories/{id}', [ProductCategoryController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_CATEGORY_DELETE]], function () {
    Route::delete('product-categories', [ProductCategoryController::class, 'destroy']);
});
