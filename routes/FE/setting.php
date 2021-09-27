<?php
use App\Http\Controllers\Api\SettingController;
Route::get('settings', [SettingController::class, 'index']);
