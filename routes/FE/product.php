<?php
use App\Http\Controllers\Api\ProductController;

Route::get('products/get-active-list-paginate', [ProductController::class, 'getActiveListPaginate']);
Route::get('products/get-all-active', [ProductController::class, 'getAllActive']);
Route::get('products/detail/{slug}', [ProductController::class, 'getBySlug']);
Route::get('products/relations', [ProductController::class, 'getAllRelations']);
