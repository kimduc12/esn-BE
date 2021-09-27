<?php
use App\Http\Controllers\Api\BrandController;

Route::get('brands/get-all', [BrandController::class, 'getAll']);
