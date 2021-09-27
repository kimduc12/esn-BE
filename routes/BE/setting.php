<?php

use App\Constants\RolePermissionConst;
use App\Http\Controllers\Api\SettingController;

Route::group(['middleware' => ['permission:'.RolePermissionConst::SETTING_EDIT]], function () {
    Route::put('settings', [SettingController::class, 'update']);
});


