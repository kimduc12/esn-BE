<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\AdvicePostController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::ADVICE_POST_LIST]], function () {
    Route::get('advice-posts', [AdvicePostController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ADVICE_POST_ADD]], function () {
    Route::post('advice-posts', [AdvicePostController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ADVICE_POST_DETAIL]], function () {
    Route::get('advice-posts/{id}', [AdvicePostController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ADVICE_POST_EDIT]], function () {
    Route::put('advice-posts/{id}', [AdvicePostController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::ADVICE_POST_DELETE]], function () {
    Route::delete('advice-posts', [AdvicePostController::class, 'destroy']);
});
