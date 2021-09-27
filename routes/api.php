<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Constants\UserConst;

Route::group(['middleware' => ['language']], function(){
    foreach (glob(__DIR__ . '/FE/*.php') as $filename) {
        require_once $filename;
    }
});

Route::group(['middleware' => ['auth:api','language','user.status:'.UserConst::STATUS_ACTIVE]], function(){
    foreach (glob(__DIR__ . '/BE/*.php') as $filename) {
        require_once $filename;
    }
});
