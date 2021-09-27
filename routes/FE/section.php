<?php
use App\Http\Controllers\Api\SectionController;

Route::get('sections/position/{position}', [SectionController::class, 'getOneByPosition']);
