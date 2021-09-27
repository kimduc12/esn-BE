<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\GiftController;

Route::get('gifts/get-active-list-paginate', [GiftController::class, 'getActiveListPaginate']);

Route::group(['middleware' => ['auth:api']], function(){
    Route::group(['middleware' => ['permission:'.RolePermissionConst::GIFT_EXCHANGE]], function () {
        Route::post('gifts/exchange', [GiftController::class, 'exchange']);
        Route::post('gifts/cancel-exchange', [GiftController::class, 'cancelExchange']);
    });
});
