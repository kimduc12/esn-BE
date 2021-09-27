<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\RoleController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::ROLE_LIST]], function () {
    Route::get('roles', [RoleController::class, 'index']);
    Route::get('roles/get-all', [RoleController::class, 'getAll']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ROLE_ADD]], function () {
    Route::post('roles', [RoleController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ROLE_DETAIL]], function () {
    Route::get('roles/{id}', [RoleController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ROLE_EDIT]], function () {
    Route::put('roles/{id}', [RoleController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ROLE_DELETE]], function () {
    Route::delete('roles', [RoleController::class, 'destroy']);
});
