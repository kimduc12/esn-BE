<?php
use App\Http\Controllers\Api\SupplierController;

Route::get('suppliers/get-all', [SupplierController::class, 'getAll']);
