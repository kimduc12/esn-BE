<?php
use App\Http\Controllers\Api\MaterialController;

Route::get('materials/get-all', [MaterialController::class, 'getAll']);
