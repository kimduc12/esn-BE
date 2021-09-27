<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\BlogController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::BLOG_LIST]], function () {
    Route::get('blogs', [BlogController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BLOG_ADD]], function () {
    Route::post('blogs', [BlogController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BLOG_DETAIL]], function () {
    Route::get('blogs/{id}', [BlogController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BLOG_EDIT]], function () {
    Route::put('blogs/{id}', [BlogController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BLOG_DELETE]], function () {
    Route::delete('blogs', [BlogController::class, 'destroy']);
});
