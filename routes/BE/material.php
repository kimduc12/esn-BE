<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\MaterialController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::MATERIAL_LIST]], function () {
    Route::get('materials', [MaterialController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::MATERIAL_ADD]], function () {
    Route::post('materials', [MaterialController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::MATERIAL_DETAIL]], function () {
    Route::get('materials/{id}', [MaterialController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::MATERIAL_EDIT]], function () {
    Route::put('materials/{id}', [MaterialController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::MATERIAL_DELETE]], function () {
    Route::delete('materials', [MaterialController::class, 'destroy']);
});
