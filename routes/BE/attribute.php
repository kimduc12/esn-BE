<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\AttributeController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::ATTRIBUTE_LIST]], function () {
    Route::get('attributes', [AttributeController::class, 'index']);
    Route::get('attributes/entity/{entity}/{entity_id}', [AttributeController::class, 'getByEntityAndEntityID']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ATTRIBUTE_ADD]], function () {
    Route::post('attributes', [AttributeController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ATTRIBUTE_DETAIL]], function () {
    Route::get('attributes/{id}', [AttributeController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ATTRIBUTE_EDIT]], function () {
    Route::put('attributes/{id}', [AttributeController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ATTRIBUTE_DELETE]], function () {
    Route::delete('attributes', [AttributeController::class, 'destroy']);
});
