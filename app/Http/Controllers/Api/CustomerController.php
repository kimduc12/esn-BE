<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Repositories\UserInterface;
use Illuminate\Support\Facades\Auth;
use Excel;

class CustomerController extends RestfulController
{
    protected $customerService;
    protected $user;
    public function __construct(CustomerService $customerService, UserInterface $userInterface){
        parent::__construct();
        $this->customerService = $customerService;
        $this->user = $userInterface;
    }

    /**
     * Get all customers with paginate
     * @group Customer management
     * @authenticated
     *
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam status integer Field to filter. Defaults to null.
     * @queryParam gender integer Field to filter. Defaults to null.
     * @queryParam is_subscribe integer Field to filter. Defaults to null.
     * @queryParam is_subscribe boolean Field to filter. Defaults to null.
     * @queryParam is_loyal boolean Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "customers": {
                *   "id": 1,
                *   "username": null,
                *   "email": null,
                *   "gender": 0,
                *   "birthday": null,
                *   "address": null,
                *   "phone": null,
                *   "status": 1,
                *   "email_verified_at": null,
                *   "phone_verified_at": null,
                *   "created_at": "2021-03-18T15:22:41.000000Z",
                *   "updated_at": "2021-03-18T15:22:41.000000Z",
                *   "name": "customer 1",
                *   "is_subscribe": 0,
                *   "sub_phone": null,
                *   "city_id": 0,
                *   "district_id": 0,
                *   "ward_id": 0,
                *   "customer_info": {
                    *   "id": 4,
                    *   "customer_id": 36,
                    *   "badge": 0,
                    *   "lastest_order_id": null,
                    *   "lastest_order_at": null,
                    *   "average_total_money_per_order": 0,
                    *   "total_spent_money": 0,
                    *   "total_orders": 0,
                    *   "total_points": 0,
                    *   "notes": null
                *   }
            * }
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
            $gender       = $request->input('gender', '');
            $is_subscribe = $request->input('is_subscribe', '');
            $is_loyal     = $request->input('is_loyal', '');
            $filter = [
                'keyword'      => $keyword,
                'status'       => $status,
                'gender'       => $gender,
                'is_subscribe' => $is_subscribe,
                'is_loyal'     => $is_loyal,
            ];

            $customers = $this->customerService->getListPaginate($perPage, $filter);

            $customers->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($customers);
            $pagingArr = $customers->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'customers' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a customer
     * @group Customer management
     * @authenticated
     *
     * @bodyParam name string required Fullname. Example: Mr A
     * @bodyParam username string . Example: Mr A
     * @bodyParam email email  . Example: mra@yopmail.com
     * @bodyParam password string  . Example: 123
     * @bodyParam phone string . Example: 0915182436
     * @bodyParam sub_phone string . Example: 0915182436
     * @bodyParam gender boolean . Example: 0
     * @bodyParam birthday date . Example: 2012-12-30
     * @bodyParam city_id integer . Example: 1
     * @bodyParam district_id integer . Example: 1
     * @bodyParam ward_id integer . Example: 1
     * @bodyParam is_subscribe boolean . Example: 0
     * @bodyParam notes string . Example: Test notes
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
            'name'         => 'bail|required',
            'username'     => 'bail|nullable|min:6|max:20|unique:users,username',
            'email'        => 'bail|nullable|email|unique:users,email',
            'password'     => 'bail|nullable|min:6|max:20',
            'phone'        => 'bail|nullable|unique:users,phone',
            'sub_phone'    => 'bail|nullable|integer',
            'gender'       => 'bail|nullable|integer',
            'birthday'     => 'bail|nullable|date_format:Y-m-d',
            'city_id'      => 'bail|nullable|exists:cities,id',
            'district_id'  => 'bail|nullable|exists:districts,id',
            'ward_id'      => 'bail|nullable|exists:wards,id',
            'is_subscribe' => 'bail|nullable|integer',
        ]);
        try{
            $data = $request->all();
            $result = $this->customerService->create($data);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get a customer by id
     * @group Customer management
     * @authenticated
     *
     * @urlParam id integer required.
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 33,
                *   "username": null,
                *   "email": null,
                *   "gender": 0,
                *   "birthday": null,
                *   "address": null,
                *   "phone": null,
                *   "status": 1,
                *   "email_verified_at": null,
                *   "phone_verified_at": null,
                *   "created_at": "2021-03-05T09:29:11.000000Z",
                *   "updated_at": "2021-03-05T09:29:11.000000Z",
                *   "name": "test",
                *   "is_subscribe": 0,
                *   "sub_phone": null,
                *   "city_id": 0,
                *   "district_id": 0,
                *   "ward_id": 0,
                *   "customer_info": {
                    *   "id": 1,
                    *   "customer_id": 33,
                    *   "badge": 0,
                    *   "lastest_order_id": null,
                    *   "lastest_order_at": null,
                    *   "average_total_money_per_order": 0,
                    *   "total_spent_money": 0,
                    *   "total_orders": 0,
                    *   "total_points": 0,
                    *   "notes": "aaaaa",
                    *   "created_at": "2021-03-05T09:29:11.000000Z",
                    *   "updated_at": "2021-03-05T09:29:11.000000Z"
                *   }
            * }
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function show($id){
        try{
            $result = $this->customerService->getByID($id);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Update a customer by user id
     * @group Customer management
     * @authenticated
     *
     * @urlParam id integer required.
     * @bodyParam name string required Fullname. Example: Mr A
     * @bodyParam username string . Example: Mr A
     * @bodyParam email email  . Example: mra@yopmail.com
     * @bodyParam password string . Example: 123
     * @bodyParam phone string . Example: 0915182436
     * @bodyParam sub_phone string . Example: 0915182436
     * @bodyParam gender boolean . Example: 0
     * @bodyParam birthday date . Example: 2012-12-30
     * @bodyParam city_id integer . Example: 1
     * @bodyParam district_id integer . Example: 1
     * @bodyParam ward_id integer . Example: 1
     * @bodyParam is_subscribe boolean . Example: 0
     * @bodyParam notes string . Example: Test notes
     * @response {
        *   "status": true,
        *   "message": "Updated"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     * @return mixed
     */
    public function update(Request $request, $id){
        $this->validate($request, [
            'name'         => 'bail|required',
            'username'     => 'bail|nullable',
            'email'        => 'bail|nullable|email',
            'password'     => 'bail|nullable|min:6|max:20',
            'phone'        => 'bail|nullable|unique:users,phone,'.$id,
            'sub_phone'    => 'bail|nullable|integer',
            'gender'       => 'bail|nullable|integer',
            'birthday'     => 'bail|nullable|date_format:Y-m-d',
            'city_id'      => 'bail|nullable|exists:cities,id',
            'district_id'  => 'bail|nullable|exists:districts,id',
            'ward_id'      => 'bail|nullable|exists:wards,id',
            'is_subscribe' => 'bail|nullable|integer'
        ]);
        try{
            $data = $request->all();
            $result = $this->customerService->updateByID($id, $data);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Delete a list of customers by an array of customer id
     * @group Customer management
     * @authenticated
     *
     * @bodyParam ids integer[] required.
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
            $ids = $request->input('ids');
            $result = $this->customerService->destroyByIDs($ids);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get user favourite products list with paginate (only active)
     * @group Customer management
     * @authenticated
     *
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
        *   "products": [
                *   {
                    *   "id": 1,
                    *   "sku": "HBN-1",
                    *   "name": "product 1",
                    *   "slug": "product-1",
                    *   "image_url": "upload/images/product.png",
                    *   "image_mobile_url": "upload/images/product-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "quote": "Lorem sipum",
                    *   "price": 10000,
                    *   "price_original": 0,
                    *   "star_rating": 5,
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_popular": 0,
                    *   "is_show": 1,
                    *   "can_promotion": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00"
                    *   "published_at": "2010-12-30 05:00:00"
                *   }
            * ]
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     * @return mixed
     */
    public function getMyFavouriteProducts(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $keyword = $request->input('keyword', '');
            $filter = [
                'keyword' => $keyword,
            ];

            $products = $this->customerService->getMyFavouriteProducts($perPage, $filter);
            $products->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($products);
            $pagingArr = $products->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'products' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get all user favourite products list (ony active)
     * @group Customer management
     * @authenticated
     *
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "sku": "HBN-1",
                    *   "name": "product 1",
                    *   "slug": "product-1",
                    *   "image_url": "upload/images/product.png",
                    *   "image_mobile_url": "upload/images/product-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "price": 10000,
                    *   "price_discount": 0,
                    *   "discount_percent": 0,
                    *   "star_rating": 5,
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_home_discount": 0,
                    *   "is_home_farvorite": 0,
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
     * @return mixed
     */
    public function getAllMyFavouriteProducts(){
        try{
            $products = $this->customerService->getAllMyFavouriteProducts();
            return $this->_response($products);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Add a product to a user as favourite product
     * @group Customer management
     * @authenticated
     * @bodyParam product_ids integer[] required Example: [1,2,3,4]
     * @response {
        *   "status": true,
        *   "message": "Added sussessfully"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function addMyFavouriteProduct(Request $request){
        $this->validate($request, [
            'product_ids'   => 'bail|required|array|min:1',
            'product_ids.*' => 'bail|required|exists:products,id',
        ]);
        try{
            $input = $request->all();
            $result = $this->customerService->addMyFavouriteProduct($input);
            if(!$result){
                return $this->_error(trans('messages.created_failed'));
            }
            return $this->_response($result, trans('messages.created_success'));
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Remove favourite products
     * @group Customer management
     * @authenticated
     * @bodyParam product_ids integer[] required Example: [1,2,3,4]
     * @response {
        *   "status": true,
        *   "message": "Deleted sussessfully"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function removeMyFavouriteProduct(Request $request){
        $this->validate($request, [
            'product_ids'   => 'bail|required|array|min:1',
            'product_ids.*' => 'bail|required|exists:products,id',
        ]);
        try{
            $input = $request->all();
            $result = $this->customerService->removeMyFavouriteProduct($input);
            if(!$result){
                return $this->_error(trans('messages.cannot_delete'));
            }
            return $this->_response($result, trans('messages.deleted_success'));
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get user read products list with paginate (only active)
     * @group Customer management
     * @authenticated
     *
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
        *   "products": [
                *   {
                    *   "id": 1,
                    *   "sku": "HBN-1",
                    *   "name": "product 1",
                    *   "slug": "product-1",
                    *   "image_url": "upload/images/product.png",
                    *   "image_mobile_url": "upload/images/product-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "price": 10000,
                    *   "price_discount": 0,
                    *   "discount_percent": 0,
                    *   "star_rating": 5,
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_home_discount": 0,
                    *   "is_home_farvorite": 0,
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
     * @return mixed
     */
    public function getMyReadProducts(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $keyword = $request->input('keyword', '');
            $filter = [
                'keyword' => $keyword,
            ];

            $products = $this->customerService->getMyReadProducts($perPage, $filter);
            $products->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($products);
            $pagingArr = $products->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'products' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get all user read products list (ony active)
     * @group Customer management
     * @authenticated
     *
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "sku": "HBN-1",
                    *   "name": "product 1",
                    *   "slug": "product-1",
                    *   "image_url": "upload/images/product.png",
                    *   "image_mobile_url": "upload/images/product-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "price": 10000,
                    *   "price_discount": 0,
                    *   "discount_percent": 0,
                    *   "star_rating": 5,
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_home_discount": 0,
                    *   "is_home_farvorite": 0,
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
     * @return mixed
     */
    public function getAllMyReadProducts(){
        try{
            $products = $this->customerService->getAllMyReadProducts();
            return $this->_response($products);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Add a product to a user as read product
     * @group Customer management
     * @authenticated
     * @bodyParam product_ids integer[] required Example: [1,2,3,4]
     * @response {
        *   "status": true,
        *   "message": "Added sussessfully"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function addMyReadProduct(Request $request){
        $this->validate($request, [
            'product_ids'   => 'bail|required|array|min:1',
            'product_ids.*' => 'bail|required|exists:products,id',
        ]);
        try{
            $input = $request->all();
            $result = $this->customerService->addMyReadProduct($input);
            if($result){
                return $this->_response($result, trans('messages.created_success'));
            }else{
                return $this->_error(trans('messages.created_failed'));
            }
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
