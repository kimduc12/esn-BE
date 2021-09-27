<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\ShippingController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::ORDER_LIST]], function () {
    Route::get('shippings', [ShippingController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ORDER_ADD]], function () {
    Route::post('shippings', [ShippingController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ORDER_DETAIL]], function () {
    Route::get('shippings/{id}', [ShippingController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ORDER_EDIT]], function () {
    Route::put('shippings/{id}', [ShippingController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ORDER_DELETE]], function () {
    Route::delete('shippings', [ShippingController::class, 'destroy']);
});
