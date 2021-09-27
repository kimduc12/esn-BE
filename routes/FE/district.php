<?php
use App\Http\Controllers\Api\DistrictController;

Route::get('districts', [DistrictController::class, 'getAll']);
