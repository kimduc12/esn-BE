<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\TopicController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::TOPIC_LIST]], function () {
    Route::get('topics', [TopicController::class, 'index']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::TOPIC_ADD]], function () {
    Route::post('topics', [TopicController::class, 'store']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::TOPIC_DETAIL]], function () {
    Route::get('topics/{id}', [TopicController::class, 'show']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::TOPIC_EDIT]], function () {
    Route::put('topics/{id}', [TopicController::class, 'update']);
});
Route::group(['middleware' => ['permission:'.RolePermissionConst::TOPIC_DELETE]], function () {
    Route::delete('topics', [TopicController::class, 'destroy']);
});
