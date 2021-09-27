<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\CountryController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::COUNTRY_LIST]], function () {
    Route::get('countries', [CountryController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::COUNTRY_ADD]], function () {
    Route::post('countries', [CountryController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::COUNTRY_DETAIL]], function () {
    Route::get('countries/{id}', [CountryController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::COUNTRY_EDIT]], function () {
    Route::put('countries/{id}', [CountryController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::COUNTRY_DELETE]], function () {
    Route::delete('countries', [CountryController::class, 'destroy']);
});
