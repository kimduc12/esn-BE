<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZaloService {
    protected $getAccessTokenURL = 'https://oauth.zaloapp.com/v3/access_token';
    protected $getUserInfoByAccessTokenURL = 'https://graph.zalo.me/v2.0/me';

    public function getAccessToken($code){
        return Http::get($this->getAccessTokenURL, [
            'app_id'       => config('services.zalo.app_id'),
            'app_secret'   => config('services.zalo.app_secret'),
            'code'         => $code,
            'redirect_uri' => config('services.zalo.redirect')
        ]);
    }

    public function getUserInfoByAccessToken($access_token){
        return Http::get($this->getUserInfoByAccessTokenURL, [
            'access_token' => $access_token,
            'fields'       => 'id,birthday,name,gender,picture',
        ]);
    }
}
