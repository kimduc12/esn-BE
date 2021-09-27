<?php

namespace App\Http\Controllers\Api;

use App\Constants\OrderConst;
use Illuminate\Http\Request;

use App\Services\OrderService;

class OrderController extends RestfulController
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }
    /**
     * Get all orders with paginate
     * @group Order management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam user_id integer Field to filter. Defaults to null.
     * @queryParam is_verified integer Field to filter. Defaults to null.
     * @queryParam channel integer Field to filter. Defaults to null.
     * @queryParam status integer Field to filter. Defaults to null.
     * @queryParam paid_status integer Field to filter. Defaults to null.
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
     *   "orders": [
     *   {
     *   "id": 1,
     *   "order_code": "",
     *   "user_id": 1,
     *   "is_gift_wrapping": 0,
     *   "status": null,
     *   "is_verified": 0,
     *   "channel": null,
     *   "total_price": 10000,
     *   "paid_status": null,
     *   "paid_at": null,
     *   "refund_at": null,
     *   "cancel_at": null,
     *   "notes": null,
     *   "created_at": "2021-02-11T13:29:46.000000Z",
     *   "updated_at": "2021-02-11T13:29:47.000000Z",
     *   "status_name": "Khởi tạo",
     *   "channel_name": "web",
     *   "paid_status_name": "",
     *   "customer": {
     *   "id": 1,
     *   "username": "superadmin",
     *   "email": "superadmin@yopmail.com",
     *   "gender": 0,
     *   "birthday": null,
     *   "address": null,
     *   "phone": "WGOBJNj3qS",
     *   "status": 1,
     *   "email_verified_at": null,
     *   "phone_verified_at": null,
     *   "created_at": "2021-02-23T15:41:36.000000Z",
     *   "updated_at": "2021-02-23T15:41:36.000000Z",
     *   "name": "super admin",
     *   "is_subscribe": 0,
     *   "sub_phone": null,
     *   "city_id": 0,
     *   "district_id": 0,
     *   "ward_id": 0,
     *   "customer_info": {
     *   "id": 1,
     *   "customer_id": 34,
     *   "badge": 0,
     *   "lastest_order_id": null,
     *   "lastest_order_at": null,
     *   "average_total_money_per_order": 0,
     *   "total_spent_money": 0,
     *   "total_orders": 0,
     *   "total_points": 0,
     *   "notes": null,
     *   "created_at": "2021-03-25T15:14:32.000000Z",
     *   "updated_at": "2021-03-25T15:14:32.000000Z"
     *   }
     *   },
     *   "shipping": {
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
     *   },
     *   "details": [
     *   {
     *   "id": 3,
     *   "order_id": 8,
     *   "product_id": 10,
     *   "product_option_id": 1,
     *   "sku": "uuu",
     *   "price": 10000,
     *   "quantity": 1,
     *   "total_price": 10000,
     *   "options": {
     *   "size": "32"
     *   },
     *   "created_at": "2021-02-11T13:29:47.000000Z",
     *   "updated_at": "2021-02-11T13:29:47.000000Z"
     *   }
     *   ]
     *   }
     * ]
     * }
     * @response status=200 scenario="Something wrong" {
     *  "status": false,
     *  "message": "Something wrong"
     * }
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input("per_page", 20) ?: 20;
            $keyword         = $request->input('keyword', '');
            $user_id         = $request->input('user_id', '');
            $is_verified     = $request->input('is_verified', '');
            $channel         = $request->input('channel', '');
            $status          = $request->input('status', '');
            $paid_status     = $request->input('paid_status', '');
            $delivery_status = $request->input('delivery_status', '');
            $cod_status      = $request->input('cod_status', '');
            $filter = [
                'keyword'         => $keyword,
                'user_id'         => $user_id,
                'is_verified'     => $is_verified,
                'channel'         => $channel,
                'status'          => $status,
                'paid_status'     => $paid_status,
                'delivery_status' => $delivery_status,
                'cod_status'      => $cod_status,
            ];
            $orders = $this->orderService->getAllPaginate($perPage, $filter);
            $orders->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($orders);
            $pagingArr = $orders->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'orders' => $pagingArr['data']
            ]);
        } catch (\Exception $e) {
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a order
     * @group Order management
     * @authenticated
     * @bodyParam user_id integer required Example: 1
     * @bodyParam is_gift_wrapping integer required Example: 1
     * @bodyParam details object[] required
     * @bodyParam details[].product_id integer required Example: 1
     * @bodyParam details[].product_option_id integer required Example: 1
     * @bodyParam details[].quantity integer required Example: 1
     * @response {
     *   "status": true,
     *   "message": "Created"
     * }
     * @response status=200 scenario="Something wrong" {
     *  "status": false,
     *  "message": "Something wrong"
     * }
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id'                     => 'bail|required|exists:users,id',
            'is_gift_wrapping'            => 'bail|required|boolean',
            'details'                     => 'bail|required|array|min:1',
            'details.*.product_id'        => 'bail|required|exists:products,id',
            'details.*.product_option_id' => 'bail|required|exists:product_options,id',
            'details.*.quantity'          => 'bail|required|integer'
        ]);
        try {
            $input = $request->all();
            $result = $this->orderService->create($input);
            if ($result) {
                return $this->_response($result, trans('messages.created_success'));
            } else {
                return $this->_error(trans('messages.created_failed'));
            }
        } catch (\Exception $e) {
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get an order by id
     * @group Order management
     * @authenticated
     * @urlParam id integer required
     * @response {
     *   "status": true,
     *   "data": {
     *   "id": 8,
     *   "user_id": 1,
     *   "is_gift_wrapping": 0,
     *   "status": 0,
     *   "total_price": 10000,
     *   "paid_at": null,
     *   "refund_at": null,
     *   "cancel_at": null,
     *   "delivering_at": null,
     *   "delivered_at": null,
     *   "created_at": "2021-02-11T13:29:46.000000Z",
     *   "updated_at": "2021-02-11T13:29:47.000000Z",
     *   "customer": {
     *   "id": 1,
     *   "username": "superadmin",
     *   "email": "superadmin@yopmail.com",
     *   "gender": 0,
     *   "birthday": null,
     *   "address": null,
     *   "phone": "70BwYFfpke",
     *   "status": 1,
     *   "name": "super admin"
     *   },
     *   "details": [
     *   {
     *   "id": 3,
     *   "order_id": 8,
     *   "product_id": 10,
     *   "product_option_id": 1,
     *   "sku": "uuu",
     *   "price": 10000,
     *   "quantity": 1,
     *   "total_price": 10000,
     *   "options": {
     *   "size": "32"
     *   }
     *   }
     *   ]
     *   }
     * }
     * @response status=200 scenario="Not found" {
     *  "status": false,
     *  "message": "Not found"
     * }
     */
    public function show($id)
    {
        try {
            $id = (int)$id;
            $result = $this->orderService->getByID($id);
            if (!$result) {
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);
        } catch (\Exception $e) {
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a order
     * @group Order management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam status integer required Example: 0
     * @bodyParam is_gift_wrapping bool Example: 0
     * @bodyParam notes string Example: 0
     * @response {
     *   "status": true,
     *   "message": "Updated"
     * }
     * @response status=200 scenario="Something wrong" {
     *  "status": false,
     *  "message": "Something wrong"
     * }
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'notes'            => 'bail|nullable',
            'is_gift_wrapping' => 'bail|nullable|boolean',
            'status'           => 'bail|required|in:' . implode(',', OrderConst::STATUS_VALIDATE),
        ]);
        try {
            $id = (int)$id;
            $input = $request->all();
            $result = $this->orderService->updateByID($id, $input);
            if ($result['status'] == false) {
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        } catch (\Exception $e) {
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete orders
     * @group Order management
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

    public function destroy(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array|min:1',
        ]);
        try {
            $arrID = $request->input('ids');
            $result = $this->orderService->destroyByIDs($arrID);
            if ($result['status'] == false) {
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        } catch (\Exception $e) {
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get my orders with paginate
     * @group Order management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @response {
     *   "status": true,
     *   "pagination": {
     *   "current_page": 1,
     *   "last_page": 1,
     *   "per_page": 3,
     *   "total": 0
     * },
     *   "orders": [
     *   {
     *   "id": 7,
     *   "user_id": 1,
     *   "is_gift_wrapping": 0,
     *   "status": 0,
     *   "total_price": 10000,
     *   "paid_at": null,
     *   "refund_at": null,
     *   "cancel_at": null,
     *   "delivering_at": null,
     *   "delivered_at": null,
     *   "created_at": "2021-02-11T12:21:23.000000Z",
     *   "updated_at": "2021-02-11T12:21:23.000000Z",
     *   "details": [
     *   {
     *   "id": 3,
     *   "order_id": 8,
     *   "product_id": 10,
     *   "product_option_id": 1,
     *   "sku": "uuu",
     *   "price": 10000,
     *   "quantity": 1,
     *   "total_price": 10000,
     *   "options": {
     *   "size": "32"
     *   }
     *   }
     *   ]
     *   }
     * ]
     * }
     * @response status=200 scenario="Something wrong" {
     *  "status": false,
     *  "message": "Something wrong"
     * }
     */
    public function getMyPaginate(Request $request)
    {
        try {
            $perPage = $request->input("per_page", 20) ?: 20;
            $filter = [];
            $orders = $this->orderService->getMyPaginate($perPage, $filter);
            $orders->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($orders);
            $pagingArr = $orders->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'orders' => $pagingArr['data']
            ]);
        } catch (\Exception $e) {
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Checkout
     * @group Order management
     * @authenticated
     * @bodyParam receiver_name string required Example: 1
     * @bodyParam receiver_phone string required Example: 1
     * @bodyParam receiver_address string required Example: 1
     * @bodyParam receiver_city_id integer required Example: 1
     * @bodyParam receiver_district_id integer required Example: 1
     * @bodyParam receiver_ward_id integer required Example: 1
     * @bodyParam payment_type integer required Example: 0
     * @bodyParam transport_type integer required Example: 0
     * @response {
     *   "status": true,
     *   "message": "Created"
     * }
     * @response status=200 scenario="Something wrong" {
     *  "status": false,
     *  "message": "Something wrong"
     * }
     */
    public function checkout(Request $request)
    {
        $this->validate($request, [
            'receiver_name'        => 'bail|required',
            'receiver_phone'       => 'bail|required',
            'receiver_address'     => 'bail|required',
            'receiver_city_id'     => 'bail|required|integer|exists:cities,id',
            'receiver_district_id' => 'bail|required|integer|exists:districts,id',
            'receiver_ward_id'     => 'bail|required|integer|exists:wards,id',
            'payment_type'         => 'bail|required|integer|in:' . implode(',', OrderConst::PAYMENT_TYPE_VALIDATE),
            'transport_type'       => 'bail|required|integer|in:' . implode(',', OrderConst::TRANSPORT_TYPE_VALIDATE),
        ]);
        try {
            $input = $request->all();
            $result = $this->orderService->checkout($input);
            if ($result['status']) {
                return $this->_response($result['data'], trans('messages.created_success'));
            } else {
                return $this->_error($result['message']);
            }
        } catch (\Exception $e) {
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
