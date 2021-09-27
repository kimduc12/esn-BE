<?php
namespace App\Http\Controllers\Api;

use App\Constants\BannerConst;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Services\BannerService;

class BannerController extends RestfulController
{
    protected $bannerService;
    public function __construct(BannerService $bannerService){
        parent::__construct();
        $this->bannerService = $bannerService;
    }
    /**
     * Get all banner with paginate
     * @group Banner management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam type string Field to filter. Defaults to 0.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "banners": [
                *   {
                    *   "id": 1,
                    *   "name": "Logo",
                    *   "link_url": "http://google.com.vn",
                    *   "image_url": "upload/images/logo.png",
                    *   "image_mobile_url": "upload/images/logo-mobile.png",
                    *   "alt": "Logo",
                    *   "sort": 0,
                    *   "type": 1,
                    *   "is_show": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "published_at": "2010-12-30 05:00:00"
                *   }
            * ]
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function index(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $type = $request->input("type", 0);
            $keyword            = $request->input('keyword', '');
            $filter = [
                'keyword'            => $keyword,
                'type'               => $type,
            ];
            $banners = $this->bannerService->getAllListBannerPaginate($perPage, $filter);
            $banners->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($banners);
            $pagingArr = $banners->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'banners' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
    * Get list of banners by type
    * @group Banner management
    * @urlParam type integer required Type: 1: Home banner, 2: Top news banner, 3: News banner
    * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Logo",
                    *   "link_url": "http://google.com.vn",
                    *   "image_url": "upload/images/logo.png",
                    *   "image_mobile_url": "upload/images/logo-mobile.png",
                    *   "alt": "Logo",
                    *   "sort": 0,
                    *   "type": 1,
                    *   "is_show": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "published_at": "2010-12-30 05:00:00"
                *   }
            * ]
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
    */
    public function getListByType(Request $request, $type){
        try{
            $banners = $this->bannerService->getListByType($type);
            return $this->_response($banners);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Create a banner image
     * @group Banner management
     * @authenticated
     * @bodyParam name string required Example: Banner 1
     * @bodyParam link_url string Example: http://google.com.vn
     * @bodyParam image_url string required Example: /upload/images/banner.png
     * @bodyParam image_mobile_url string Example: /upload/images/banner-mobile.png
     * @bodyParam alt string required Example: Logo
     * @bodyParam sort integer required Example: 0
     * @bodyParam type integer required Example: 0
     * @bodyParam is_show bool required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam published_at datetime required Example: 2010-10-20 20:00:00
     * @response {
        *   "status": true,
        *   "message": "Created"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function store(Request $request){
        $this->validate($request, [
            'name'             => 'bail|required',
            'link_url'         => 'bail|nullable',
            'image_url'        => 'bail|required',
            'image_mobile_url' => 'bail|nullable',
            'alt'              => 'bail|required',
            'sort'             => 'bail|required|integer',
            'type'             => 'bail|required|integer|in:'.implode(',', BannerConst::TYPE_VALIDATE),
            'is_show'          => 'bail|required|boolean',
            'status'           => 'bail|required|in:'.implode(',', BannerConst::STATUS_VALIDATE),
            'published_at'     => 'bail|required|date_format:Y-m-d H:i:s'
        ]);
        try{
            $input = $request->all();

            $result = $this->bannerService->createNewBanner($input);
            if($result){
                return $this->_response($result, trans('messages.created_success'));
            }else{
                return $this->_error(trans('messages.created_failed'));
            }
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get a banner by id
     * @group Banner management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Logo",
                *   "link_url": "http://google.com.vn",
                *   "image_url": "upload/images/logo.png",
                *   "image_mobile_url": "upload/images/logo-mobile.png",
                *   "alt": "Logo",
                *   "sort": 0,
                *   "type": 1,
                *   "is_show": 0,
                *   "status": 1,
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00",
                *   "published_at": "2010-12-30 05:00:00"
            *   }
        * }
     * @response status=200 scenario="Not found" {
        *  "status": false,
        *  "message": "Not found"
        * }
     */
    public function show($id){
        try{
            $id = (int)$id;
            $result = $this->bannerService->getBannerByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a banner
     * @group Banner management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: Banner 1
     * @bodyParam link_url string Example: http://google.com.vn
     * @bodyParam image_url string required Example: /upload/images/banner.png
     * @bodyParam image_mobile_url string Example: /upload/images/banner-mobile.png
     * @bodyParam alt string required Example: Logo
     * @bodyParam sort integer required Example: 0
     * @bodyParam type integer required Example: 0
     * @bodyParam is_show bool required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam published_at datetime required Example: 2010-10-20 20:00:00
     * @response {
        *   "status": true,
        *   "message": "Updated"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function update(Request $request, $id){
        $this->validate($request, [
            'name'             => 'bail|required',
            'link_url'         => 'bail|nullable',
            'image_url'        => 'bail|required',
            'image_mobile_url' => 'bail|nullable',
            'alt'              => 'bail|required',
            'sort'             => 'bail|required|integer',
            'type'             => 'bail|required|integer|in:'.implode(',', BannerConst::TYPE_VALIDATE),
            'is_show'          => 'bail|required|boolean',
            'status'           => 'bail|required|in:'.implode(',', BannerConst::STATUS_VALIDATE),
            'published_at'     => 'bail|required|date_format:Y-m-d H:i:s'
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->bannerService->updateBannerByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete a banner
     * @group Banner management
     * @authenticated
     * @bodyParam ids integer[] required
     * @response {
        *   "status": true,
        *   "message": "Deleted"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */

    public function destroy(Request $request){
        $this->validate($request, [
            'ids' => 'required|array|min:1',
        ]);
        try{
            $arrID= $request->input('ids');
            $result = $this->bannerService->destroyBannerByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }


}
