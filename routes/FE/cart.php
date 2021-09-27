<?php
use App\Http\Controllers\Api\CartController;

Route::group(['middleware' => ['auth:api']], function(){
    Route::get('carts/mine', [CartController::class, 'getMyCart']);
    Route::post('carts', [CartController::class, 'store']);
    Route::put('carts', [CartController::class, 'update']);
    Route::delete('carts/detail', [CartController::class, 'removeDetail']);
    Route::delete('carts', [CartController::class, 'destroy']);
});
