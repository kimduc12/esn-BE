<?php
use App\Http\Controllers\Api\ProductCategoryController;

Route::get('product-categories/get-all-active', [ProductCategoryController::class, 'getAllActive']);
Route::get('product-categories/slug/{slug}', [ProductCategoryController::class, 'getBySlug']);
