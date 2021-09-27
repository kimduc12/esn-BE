<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\ProductController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_LIST]], function () {
    Route::get('products', [ProductController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_ADD]], function () {
    Route::post('products', [ProductController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_DETAIL]], function () {
    Route::get('products/{id}', [ProductController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_EDIT]], function () {
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::post('products/{id}/options', [ProductController::class, 'storeOption']);
    Route::put('products/{id}/options/{option_id}', [ProductController::class, 'updateOption']);
    Route::delete('products/{id}/options', [ProductController::class, 'destroyOptions']);
    Route::put('products/{id}/files', [ProductController::class, 'updateFiles']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PRODUCT_DELETE]], function () {
    Route::delete('products', [ProductController::class, 'destroy']);
});
