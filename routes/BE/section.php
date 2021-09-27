<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\SectionController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::SECTION_LIST]], function () {
    Route::get('sections', [SectionController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::SECTION_ADD]], function () {
    Route::post('sections', [SectionController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::SECTION_DETAIL]], function () {
    Route::get('sections/{id}', [SectionController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::SECTION_EDIT]], function () {
    Route::put('sections/{id}', [SectionController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::SECTION_DELETE]], function () {
    Route::delete('sections', [SectionController::class, 'destroy']);
});
