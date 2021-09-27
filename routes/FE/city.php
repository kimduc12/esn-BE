<?php
use App\Http\Controllers\Api\CityController;

Route::get('cities', [CityController::class, 'getAll']);
