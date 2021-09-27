<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\SupplierController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::SUPPLIER_LIST]], function () {
    Route::get('suppliers', [SupplierController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::SUPPLIER_ADD]], function () {
    Route::post('suppliers', [SupplierController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::SUPPLIER_DETAIL]], function () {
    Route::get('suppliers/{id}', [SupplierController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::SUPPLIER_EDIT]], function () {
    Route::put('suppliers/{id}', [SupplierController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::SUPPLIER_DELETE]], function () {
    Route::delete('suppliers', [SupplierController::class, 'destroy']);
});
