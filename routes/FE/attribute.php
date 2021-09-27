<?php
use App\Http\Controllers\Api\AttributeController;

Route::get('attributes/entity/{entity}', [AttributeController::class, 'getByEntity']);
