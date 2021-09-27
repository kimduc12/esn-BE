<?php
namespace App\Http\Controllers\Api;

use App\Constants\GiftConst;
use Illuminate\Http\Request;

use App\Services\GiftService;

class GiftController extends RestfulController
{
    protected $giftService;
    public function __construct(GiftService $giftService){
        parent::__construct();
        $this->giftService = $giftService;
    }
    /**
     * Get all gifts with paginate
     * @group Gift management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam status integer Field to filter. Defaults to null.=
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "total_price": 100000,
        *   "gifts": [
                *   {
                    *   "id": 1,
                    *   "supplier_id": 1,
                    *   "code": "G001",
                    *   "name": "Xe cho bé",
                    *   "image_url": "/images/xe.png",
                    *   "image_mobile_url": "/images/xe.png",
                    *   "min_user_badge": 1,
                    *   "quantity": 1,
                    *   "points": 100,
                    *   "price": 10000,
                    *   "start_datetime": "2000-10-20 00:00:00",
                    *   "end_datetime": "2000-10-20 00:00:00",
                    *   "is_never_expired": 0,
                    *   "content":"lorem isum",
                    *   "sort": 1,
                    *   "status": 1,
                    *   "total_exchange": 0,
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
            $keyword      = $request->input('keyword', '');
            $status       = $request->input('status', '');
            $filter = [
                'keyword'      => $keyword,
                'status'       => $status,
            ];
            $total_price = $this->giftService->getTotalPrice($filter);
            $gifts = $this->giftService->getAllPaginate($perPage, $filter);
            $gifts->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($gifts);
            $pagingArr = $gifts->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'gifts' => $pagingArr['data'],
                'total_price' => $total_price,
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a gift
     * @group Gift management
     * @authenticated
     * @bodyParam supplier_id integer Example: 1
     * @bodyParam name string required Example: Xe cho bé
     * @bodyParam image_url string required Example: /upload/images/product.png
     * @bodyParam image_mobile_url string Example: /upload/images/product-mobile.png
     * @bodyParam min_user_badge integer required Example: 0
     * @bodyParam quantity integer required Example: 0
     * @bodyParam points integer required Example: 0
     * @bodyParam price integer required Example: 0
     * @bodyParam start_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam end_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam content string Example: Lorem ipsum
     * @bodyParam sort integer Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam published_at datetime required Example: 2021-10-20 00:00:00
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
            'supplier_id'      => 'bail|nullable|exists:suppliers,id',
            'name'             => 'bail|required|unique:gifts,name',
            'image_url'        => 'bail|required',
            'image_mobile_url' => 'bail|nullable',
            'min_user_badge'   => 'bail|required|integer',
            'quantity'         => 'bail|required|integer',
            'points'           => 'bail|required|integer',
            'price'            => 'bail|required|integer',
            'start_datetime'   => 'bail|nullable|date_format:Y-m-d H:i:s',
            'end_datetime'     => 'bail|nullable|date_format:Y-m-d H:i:s',
            'content'          => 'bail|nullable',
            'sort'             => 'bail|nullable|integer',
            'status'           => 'bail|required|in:'.implode(',', GiftConst::STATUS_VALIDATE),
            'published_at'     => 'bail|required|date_format:Y-m-d H:i:s',
        ]);
        try{
            $input = $request->all();

            $result = $this->giftService->create($input);
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
     * Get a gift by id
     * @group Gift management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "supplier_id": 1,
                *   "code": "G001",
                *   "name": "Xe cho bé",
                *   "image_url": "/images/xe.png",
                *   "image_mobile_url": "/images/xe.png",
                *   "min_user_badge": 1,
                *   "quantity": 1,
                *   "points": 100,
                *   "price": 10000,
                *   "start_datetime": "2000-10-20 00:00:00",
                *   "end_datetime": "2000-10-20 00:00:00",
                *   "is_never_expired": 0,
                *   "content":"lorem isum",
                *   "sort": 1,
                *   "status": 1,
                *   "total_exchange": 0,
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
            $result = $this->giftService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a gift
     * @group Gift management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam supplier_id integer Example: 1
     * @bodyParam name string required Example: Xe cho bé
     * @bodyParam image_url string required Example: /upload/images/product.png
     * @bodyParam image_mobile_url string Example: /upload/images/product-mobile.png
     * @bodyParam min_user_badge integer required Example: 0
     * @bodyParam quantity integer required Example: 0
     * @bodyParam points integer required Example: 0
     * @bodyParam price integer required Example: 0
     * @bodyParam start_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam end_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam content string Example: Lorem ipsum
     * @bodyParam sort integer Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam published_at datetime required Example: 2021-10-20 00:00:00
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
            'supplier_id'      => 'bail|nullable|exists:suppliers,id',
            'name'             => 'bail|required|unique:gifts,name,'.$id,
            'image_url'        => 'bail|required',
            'image_mobile_url' => 'bail|nullable',
            'min_user_badge'   => 'bail|required|integer',
            'quantity'         => 'bail|required|integer',
            'points'           => 'bail|required|integer',
            'price'            => 'bail|required|integer',
            'start_datetime'   => 'bail|nullable|date_format:Y-m-d H:i:s',
            'end_datetime'     => 'bail|nullable|date_format:Y-m-d H:i:s',
            'content'          => 'bail|nullable',
            'sort'             => 'bail|nullable|integer',
            'status'           => 'bail|required|in:'.implode(',', GiftConst::STATUS_VALIDATE),
            'published_at'     => 'bail|required|date_format:Y-m-d H:i:s',
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->giftService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete gifts
     * @group Gift management
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
            $result = $this->giftService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get gifts with paginate (only active)
     * @group Gift management
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
        *   "gifts": [
                *   {
                    *   "id": 1,
                    *   "supplier_id": 1,
                    *   "code": "G001",
                    *   "name": "Xe cho bé",
                    *   "image_url": "/images/xe.png",
                    *   "image_mobile_url": "/images/xe.png",
                    *   "min_user_badge": 1,
                    *   "quantity": 1,
                    *   "points": 100,
                    *   "price": 10000,
                    *   "start_datetime": "2000-10-20 00:00:00",
                    *   "end_datetime": "2000-10-20 00:00:00",
                    *   "is_never_expired": 0,
                    *   "content":"lorem isum",
                    *   "sort": 1,
                    *   "status": 1,
                    *   "total_exchange": 0,
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
    public function getActiveListPaginate(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $keyword      = $request->input('keyword', '');
            $filter = [
                'keyword' => $keyword
            ];
            $gifts = $this->giftService->getActiveListPaginate($perPage, $filter);
            $gifts->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($gifts);
            $pagingArr = $gifts->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'gifts' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Exchange a gift
     * @group Gift management
     * @authenticated
     * @bodyParam id integer required Example: 1
     * @response {
        *   "status": true,
        *   "message": "Success"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function exchange(Request $request){
        $this->validate($request, [
            'id' => 'bail|required|exists:gifts,id'
        ]);
        try{
            $input = $request->all();

            $result = $this->giftService->exchange($input['id']);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Return a gift
     * @group Gift management
     * @authenticated
     * @bodyParam id integer required Example: 1
     * @response {
        *   "status": true,
        *   "message": "Success"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function cancelExchange(Request $request){
        $this->validate($request, [
            'id' => 'bail|required|exists:gift_exchange,id'
        ]);
        try{
            $input = $request->all();

            $result = $this->giftService->cancelExchange($input['id']);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
