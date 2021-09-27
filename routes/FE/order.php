<?php
use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\OrderController;

Route::group(['middleware' => ['auth:api']], function(){
    Route::get('orders/mine', [OrderController::class, 'getMyPaginate']);
    Route::post('orders/checkout', [OrderController::class, 'checkout']);
});
