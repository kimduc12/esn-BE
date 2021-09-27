<?php
use App\Http\Controllers\Api\BlogCategoryController;

Route::get('blog-categories/get-all-active', [BlogCategoryController::class, 'getAllActive']);
Route::get('blog-categories/slug/{slug}', [BlogCategoryController::class, 'getBySlug']);
