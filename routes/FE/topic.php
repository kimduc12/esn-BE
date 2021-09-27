<?php
use App\Http\Controllers\Api\TopicController;

Route::get('topics/get-all', [TopicController::class, 'getAll']);
Route::get('topics/get-one-active', [TopicController::class, 'getOneActive']);
