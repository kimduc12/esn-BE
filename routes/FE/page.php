<?php
use App\Http\Controllers\Api\PageController;

Route::get('pages/get-list-paginate', [PageController::class, 'getListPaginate']);
Route::get('pages/get-all', [PageController::class, 'getAll']);
Route::get('pages/detail/{slug}', [PageController::class, 'getBySlug']);
