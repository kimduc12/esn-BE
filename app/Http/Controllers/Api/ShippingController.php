<?php
namespace App\Http\Controllers\Api;

use App\Constants\ShippingConst;
use Illuminate\Http\Request;

use App\Services\ShippingService;

class ShippingController extends RestfulController
{
    protected $shippingService;
    public function __construct(ShippingService $shippingService){
        parent::__construct();
        $this->shippingService = $shippingService;
    }
    /**
     * Get all shippings with paginate
     * @group Shipping management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam order_id integer Field to filter. Defaults to null.
     * @queryParam status integer Field to filter. Defaults to null.
     * @queryParam delivery_status integer Field to filter. Defaults to null.
     * @queryParam cod_status integer Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "shippings": [
                *   {
                    *   "id": 1,
                    *   "order_id": 1,
                    *   "shipping_code": "S20210000001",
                    *   "receiver_name": "Nguyen Van A",
                    *   "receiver_phone": "0915182435",
                    *   "from_address": "150 Nguyen Van Troi",
                    *   "lat_from_address": "123456",
                    *   "lng_from_address": "123456",
                    *   "to_address": "150 Dien Bien Phu",
                    *   "lat_to_address": "123456",
                    *   "lng_to_address": "123456",
                    *   "expect_pickup_at": null,
                    *   "pickup_at": null,
                    *   "expect_delivered_at": null,
                    *   "delivered_at": null,
                    *   "delivery_status": 0,
                    *   "cod_status": 0,
                    *   "cod_price": 0,
                    *   "shipping_fee": 0,
                    *   "notes": null,
                    *   "status": 0,
                    *   "created_at": "2021-02-11T12:21:23.000000Z",
                    *   "updated_at": "2021-02-11T12:21:23.000000Z",
                    *   "status_name": "Khởi tạo",
                    *   "cod_status_name": ""
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
            $keyword         = $request->input('keyword', '');
            $order_id         = $request->input('order_id', '');
            $status          = $request->input('status', '');
            $delivery_status = $request->input('delivery_status', '');
            $cod_status      = $request->input('cod_status', '');
            $filter = [
                'keyword'         => $keyword,
                'order_id'         => $order_id,
                'status'          => $status,
                'delivery_status' => $delivery_status,
                'cod_status'      => $cod_status,
            ];
            $shippings = $this->shippingService->getAllPaginate($perPage, $filter);
            $shippings->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($shippings);
            $pagingArr = $shippings->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'shippings' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a shipping
     * @group Shipping management
     * @authenticated
     * @bodyParam order_id integer required Example: 1
     * @bodyParam receiver_name string required Example: Nguyen Van A
     * @bodyParam receiver_phone string required Example: 0915182435
     * @bodyParam from_address string required Example: 190 Nguyen Van Troi
     * @bodyParam lat_from_address string required Example: 123456
     * @bodyParam lng_from_address string required Example: 123456
     * @bodyParam to_address string required Example: 190 Dien Bien Phu
     * @bodyParam lat_to_address string required Example: 123456
     * @bodyParam lng_to_address string required Example: 123456
     * @bodyParam expect_pickup_at datetime required Example: 2010-10-20 20:00:00
     * @bodyParam expect_delivered_at datetime required Example: 2010-10-20 20:00:00
     * @bodyParam cod_price integer required Example: 0
     * @bodyParam shipping_fee integer required Example: 0
     * @bodyParam notes integer required Example: Test
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
            'order_id'            => 'bail|required|exists:orders,id',
            'receiver_name'       => 'bail|required|max:191',
            'receiver_phone'      => 'bail|required|max:191',
            'from_address'        => 'bail|required|max:191',
            'lat_from_address'    => 'bail|required|max:191',
            'lng_from_address'    => 'bail|required|max:191',
            'to_address'          => 'bail|required|max:191',
            'lat_to_address'      => 'bail|required|max:191',
            'lng_to_address'      => 'bail|required|max:191',
            'expect_pickup_at'    => 'bail|nullable|date_format:Y-m-d H:i:s',
            'expect_delivered_at' => 'bail|nullable|date_format:Y-m-d H:i:s',
            'cod_price'           => 'bail|nullable|integer',
            'shipping_fee'        => 'bail|nullable|integer',
            'notes'               => 'bail|nullable',
        ]);
        try{
            $input = $request->all();
            $result = $this->shippingService->create($input);
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
     * Get an shipping by id
     * @group Shipping management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "order_id": 1,
                *   "shipping_code": "S20210000001",
                *   "receiver_name": "Nguyen Van A",
                *   "receiver_phone": "0915182435",
                *   "from_address": "150 Nguyen Van Troi",
                *   "lat_from_address": "123456",
                *   "lng_from_address": "123456",
                *   "to_address": "150 Dien Bien Phu",
                *   "lat_to_address": "123456",
                *   "lng_to_address": "123456",
                *   "expect_pickup_at": null,
                *   "pickup_at": null,
                *   "expect_delivered_at": null,
                *   "delivered_at": null,
                *   "delivery_status": 0,
                *   "cod_status": 0,
                *   "cod_price": 0,
                *   "shipping_fee": 0,
                *   "notes": null,
                *   "status": 0,
                *   "created_at": "2021-02-11T12:21:23.000000Z",
                *   "updated_at": "2021-02-11T12:21:23.000000Z",
                *   "status_name": "Khởi tạo",
                *   "cod_status_name": ""
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
            $result = $this->shippingService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a shipping
     * @group Shipping management
     * @authenticated
     * @urlParam id integer required
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
            'order_id'            => 'bail|required|exists:orders,id,'.$id,
            'receiver_name'       => 'bail|required|max:191',
            'receiver_phone'      => 'bail|required|max:191',
            'from_address'        => 'bail|required|max:191',
            'lat_from_address'    => 'bail|required|max:191',
            'lng_from_address'    => 'bail|required|max:191',
            'to_address'          => 'bail|required|max:191',
            'lat_to_address'      => 'bail|required|max:191',
            'lng_to_address'      => 'bail|required|max:191',
            'expect_pickup_at'    => 'bail|nullable|date_format:Y-m-d H:i:s',
            'expect_delivered_at' => 'bail|nullable|date_format:Y-m-d H:i:s',
            'cod_price'           => 'bail|nullable|integer',
            'shipping_fee'        => 'bail|nullable|integer',
            'notes'               => 'bail|nullable',
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->shippingService->updateByID($id, $input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Delete shippings
     * @group Shipping management
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
            $result = $this->shippingService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
