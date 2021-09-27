<?php
use App\Http\Controllers\Api\CustomerController;

Route::group(['middleware' => ['auth:api']], function(){
    Route::get('customers/favourite-products/all', [CustomerController::class, 'getAllMyFavouriteProducts']);
    Route::get('customers/favourite-products', [CustomerController::class, 'getMyFavouriteProducts']);
    Route::post('customers/favourite-products', [CustomerController::class, 'addMyFavouriteProduct']);
    Route::delete('customers/favourite-products', [CustomerController::class, 'removeMyFavouriteProduct']);

    Route::get('customers/read-products/all', [CustomerController::class, 'getAllMyReadProducts']);
    Route::get('customers/read-products', [CustomerController::class, 'getMyReadProducts']);
    Route::post('customers/read-products', [CustomerController::class, 'addMyReadProduct']);
});
