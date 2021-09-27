<?php
namespace App\Http\Controllers\Api;

use App\Constants\CountryConst;
use Illuminate\Http\Request;

use App\Services\CountryService;

class CountryController extends RestfulController
{
    protected $countryService;
    public function __construct(CountryService $countryService){
        parent::__construct();
        $this->countryService = $countryService;
    }
    /**
     * Get all countries with paginate
     * @group Country management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "countries": [
                *   {
                    *   "id": 1,
                    *   "name": "Viet Nam",
                    *   "image_url": "upload/images/vn.png",
                    *   "image_mobile_url": "upload/images/vn-mobile.png",
                    *   "sort": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00"
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
            $keyword            = $request->input('keyword', '');
            $filter = [
                'keyword'            => $keyword,
            ];
            $countries = $this->countryService->getAllPaginate($perPage, $filter);
            $countries->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($countries);
            $pagingArr = $countries->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'countries' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a country
     * @group Country management
     * @authenticated
     * @bodyParam name string required Example: Viet Nam
     * @bodyParam image_url string Example: /upload/images/vn.png
     * @bodyParam image_mobile_url string Example: /upload/images/vn-mobile.png
     * @bodyParam sort integer required Example: 0
     * @bodyParam status integer required Example: 0
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
            'name'             => 'bail|required|unique:countries,name',
            'image_url'        => 'bail|nullable',
            'image_mobile_url' => 'bail|nullable',
            'sort'             => 'bail|required|integer',
            'status'           => 'bail|required|in:'.implode(',', CountryConst::STATUS_VALIDATE),
        ]);
        try{
            $input = $request->all();

            $result = $this->countryService->create($input);
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
     * Get a country by id
     * @group Country management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Viet Nam",
                *   "image_url": "upload/images/vn.png",
                *   "image_mobile_url": "upload/images/vn-mobile.png",
                *   "sort": 0,
                *   "status": 1,
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00"
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
            $result = $this->countryService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a country
     * @group Country management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: Viet Nam
     * @bodyParam image_url string Example: /upload/images/vn.png
     * @bodyParam image_mobile_url string Example: /upload/images/vn-mobile.png
     * @bodyParam sort integer required Example: 0
     * @bodyParam status integer required Example: 0
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
            'name'             => 'bail|required|unique:countries,name,'.$id,
            'image_url'        => 'bail|nullable',
            'image_mobile_url' => 'bail|nullable',
            'sort'             => 'bail|required|integer',
            'status'           => 'bail|required|in:'.implode(',', CountryConst::STATUS_VALIDATE)
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->countryService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete countries
     * @group Country management
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
            $result = $this->countryService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get countries (only active)
     * @group Country management
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Viet Nam",
                    *   "image_url": "upload/images/vn.png",
                    *   "image_mobile_url": "upload/images/vn-mobile.png",
                    *   "sort": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00"
                *   }
            * ]
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function getAllActive(Request $request){
        try{
            //$category_id = $request->input('category_id', 0);
            $filter = [

            ];
            $countries = $this->countryService->getAllActive($filter);
            return $this->_response($countries);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
