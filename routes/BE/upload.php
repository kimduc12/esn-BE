<?php
use App\Http\Controllers\Api\UploadController;

Route::post('uploads', [UploadController::class, 'uploadStorage']);
Route::delete('uploads', [UploadController::class, 'deleteFile']);
