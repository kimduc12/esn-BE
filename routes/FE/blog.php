<?php
use App\Http\Controllers\Api\BlogController;

Route::get('blogs/list-paginate', [BlogController::class, 'getListPaginate']);
Route::get('blogs/all', [BlogController::class, 'getAll']);
Route::get('blogs/detail/{slug}', [BlogController::class, 'getBySlug']);
