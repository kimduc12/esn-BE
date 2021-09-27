<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\CustomerController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::CUSTOMER_LIST]], function () {
    Route::get('customers', [CustomerController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::CUSTOMER_ADD]], function () {
    Route::post('customers', [CustomerController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::CUSTOMER_DETAIL]], function () {
    Route::get('customers/{id}', [CustomerController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::CUSTOMER_EDIT]], function () {
    Route::put('customers/{id}', [CustomerController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::CUSTOMER_DELETE]], function () {
    Route::delete('customers', [CustomerController::class, 'destroy']);
});
