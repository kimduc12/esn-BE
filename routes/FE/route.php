<?php
use App\Http\Controllers\Api\RouteController;

Route::get('routes/get-all', [RouteController::class, 'getAll']);
Route::get('routes/{slug}', [RouteController::class, 'getBySlug']);
