<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\PatternController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::PATTERN_LIST]], function () {
    Route::get('patterns', [PatternController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PATTERN_ADD]], function () {
    Route::post('patterns', [PatternController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PATTERN_DETAIL]], function () {
    Route::get('patterns/{id}', [PatternController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PATTERN_EDIT]], function () {
    Route::put('patterns/{id}', [PatternController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PATTERN_DELETE]], function () {
    Route::delete('patterns', [PatternController::class, 'destroy']);
});
