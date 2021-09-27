<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\PageController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::PAGE_LIST]], function () {
    Route::get('pages', [PageController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PAGE_ADD]], function () {
    Route::post('pages', [PageController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PAGE_DETAIL]], function () {
    Route::get('pages/{id}', [PageController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PAGE_EDIT]], function () {
    Route::put('pages/{id}', [PageController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PAGE_DELETE]], function () {
    Route::delete('pages', [PageController::class, 'destroy']);
});
