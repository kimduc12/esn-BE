<?php
use App\Http\Controllers\Api\AgeController;

Route::get('ages/get-all', [AgeController::class, 'getAll']);
