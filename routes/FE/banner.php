<?php
use App\Http\Controllers\Api\BannerController;

Route::get('banners/type/{type}', [BannerController::class, 'getListByType']);
