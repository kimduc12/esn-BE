<?php
use App\Http\Controllers\Api\CountryController;

Route::get('countries/get-all-active', [CountryController::class, 'getAllActive']);
