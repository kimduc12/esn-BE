<?php
use App\Http\Controllers\Api\WardController;

Route::get('wards', [WardController::class, 'getAll']);
