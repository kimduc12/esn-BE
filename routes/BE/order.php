<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\OrderController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::ORDER_LIST]], function () {
    Route::get('orders', [OrderController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ORDER_ADD]], function () {
    Route::post('orders', [OrderController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ORDER_DETAIL]], function () {
    Route::get('orders/{id}', [OrderController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ORDER_EDIT]], function () {
    Route::put('orders/{id}', [OrderController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ORDER_DELETE]], function () {
    Route::delete('orders', [OrderController::class, 'destroy']);
});
