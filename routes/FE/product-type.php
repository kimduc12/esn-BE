<?php
use App\Http\Controllers\Api\ProductTypeController;

Route::get('product-types/get-all', [ProductTypeController::class, 'getAll']);
