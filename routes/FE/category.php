<?php

use App\Http\Controllers\Api\ProductCategoryController;

Route::get('categories/get-all-active', [ProductCategoryController::class, 'getAllActive']);
Route::get('categories/slug/{slug}', [ProductCategoryController::class, 'getBySlug']);
