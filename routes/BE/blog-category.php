<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\BlogCategoryController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::BLOG_CATEGORY_LIST]], function () {
    Route::get('blog-categories', [BlogCategoryController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BLOG_CATEGORY_ADD]], function () {
    Route::post('blog-categories', [BlogCategoryController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BLOG_CATEGORY_DETAIL]], function () {
    Route::get('blog-categories/{id}', [BlogCategoryController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BLOG_CATEGORY_EDIT]], function () {
    Route::put('blog-categories/{id}', [BlogCategoryController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::BLOG_CATEGORY_DELETE]], function () {
    Route::delete('blog-categories', [BlogCategoryController::class, 'destroy']);
});
