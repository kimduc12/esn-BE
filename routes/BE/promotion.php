<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\PromotionController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::PROMOTION_LIST]], function () {
    Route::get('promotions', [PromotionController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PROMOTION_ADD]], function () {
    Route::post('promotions/code-type', [PromotionController::class, 'storeCode']);
    Route::post('promotions/program-type', [PromotionController::class, 'storeProgram']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PROMOTION_DETAIL]], function () {
    Route::get('promotions/{id}', [PromotionController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PROMOTION_EDIT]], function () {
    Route::put('promotions/code-type/{id}', [PromotionController::class, 'updateCode']);
    Route::put('promotions/program-type/{id}', [PromotionController::class, 'updateProgram']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::PROMOTION_DELETE]], function () {
    Route::delete('promotions', [PromotionController::class, 'destroy']);
});
