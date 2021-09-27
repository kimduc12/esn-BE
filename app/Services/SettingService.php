<?php
namespace App\Services;

use App\Repositories\SettingInterface;
use App\Models\Setting;

class SettingService extends BaseService {
    protected $setting;
    function __construct(
        SettingInterface $setting
    ){
        $this->setting = $setting;
    }


    public function updateSetting($data){
        $setting = Setting::first();
        if (!$setting) {
            $this->setting->create($data);
        }

        $this->setting->update($setting->id, $data);
        $setting = Setting::first();
        return $this->_result(true, trans('messages.update_success'), $setting);
    }




}
