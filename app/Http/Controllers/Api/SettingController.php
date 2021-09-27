<?php
namespace App\Http\Controllers\Api;

use App\Services\SettingService;
use Illuminate\Http\Request;

use App\Models\Setting;

class SettingController extends RestfulController
{
    protected $settingService;
    public function __construct(SettingService $settingService){
        parent::__construct();
        $this->settingService = $settingService;
    }
    /**
    * Get setting config
    * @group Setting management
    * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "title": "Baby Shop",
                *   "keyword": "Baby Short, Baby Skirt",
                *   "description": "Please buy our product",
                *   "logo_url": "/upload/images/logo.png",
                *   "logo_mobile_url": "/upload/images/logo-mobile.png",
                *   "bg_footer_url": "/upload/images/bg-footer.png",
                *   "bg_footer_mobile_url": "/upload/images/bg-footer-mobile.png",
                *   "bg_home_hot_news_url": "/upload/images/bg-hot-news.png",
                *   "bg_home_hot_news_mobile_url": "/upload/images/bg-hot-news-mobile.png"
            * }
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
    */
    public function index(){
        try{
            $setting = Setting::first();
            return $this->_response($setting);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Update setting config
     * @group Setting management
     * @authenticated
     *
    * @bodyParam title string required Meta title. Example: Baby Shop
    * @bodyParam keyword string required Meta keyword. Example: Baby Shop | Baby shirt
    * @bodyParam description string required Meta description. Example: Please buy our product
    * @bodyParam logo_url string required Logo website. Example: /upload/images/logo.png
    * @bodyParam logo_mobile_url string Logo website for mobile. Example: /upload/images/logo-mobile.png
    * @bodyParam bg_footer_url string required Background image in footer. Example: /upload/images/bg-footer.png
    * @bodyParam bg_footer_mobile_url string Background image in footer for mobile. Example: /upload/images/bg-footer-mobile.png
    * @bodyParam bg_home_hot_news_url string required Background image home hot news. Example: /upload/images/bg-hot-news.png
    * @bodyParam bg_home_hot_news_mobile_url string Background image home hot news for mobile. Example: /upload/images/bg-hot-news-mobile.png
    * @response {
        *   "status": true,
        *   "message": "Updated"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function update(Request $request){
        $this->validate($request, [
            'title'                       => 'bail|required',
            'keyword'                     => 'bail|required',
            'description'                 => 'bail|required',
            'logo_url'                    => 'bail|required',
            'logo_mobile_url'             => 'bail|nullable',
            'bg_footer_url'               => 'bail|required',
            'bg_footer_mobile_url'        => 'bail|nullable',
            'bg_home_hot_news_url'        => 'bail|required',
            'bg_home_hot_news_mobile_url' => 'bail|nullable',

        ]);
        try{
            $input = $request->all();
            if(count($input)==0) {
                return $this->_error(trans('messages.notthing_update'));
            }
            $result = $this->settingService->updateSetting($input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data'], trans('messages.update_success'));
        } catch(\Exception $e) {
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

}
