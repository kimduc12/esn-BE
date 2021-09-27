<?php
use App\Http\Controllers\Api\PatternController;

Route::get('patterns/get-all', [PatternController::class, 'getAll']);
