<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Services\CartService;

class CartController extends RestfulController
{
    protected $cartService;
    public function __construct(CartService $cartService){
        parent::__construct();
        $this->cartService = $cartService;
    }

    /**
     * Get all carts with paginate
     * @group Cart management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam user_id integer Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "cart": {
                *   "user_id": 1,
                *   "is_gift_wrapping": 0,
                *   "gift_message": "",
                *   "total_price": 10000,
                *   "discount_total_price": 0,
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00",
                *   "details": [
                    *   {
                        *   "id": 1,
                        *   "cart_id": 1,
                        *   "product_id": 1,
                        *   "product_option_id": 1,
                        *   "price": 10000,
                        *   "total_price": 10000,
                        *   "discount_total_price": 0,
                        *   "promotion_id": 0,
                        *   "quantity": 1,
                        *   "created_at": "2010-12-30 05:00:00",
                        *   "updated_at": "2010-12-30 05:00:00",
                        *   "product": {
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
                            *   "sku": "123456",
                            *   "barcode": "123456",
                            *   "stock_address": "100 Hai Bà Trưng",
                            *   "quantity": 5000,
                            *   "star_rating": 5,
                            *   "title": "Lorem sipum",
                            *   "keyword": "Lorem sipum",
                            *   "description": "Lorem sipum",
                            *   "is_show": 0,
                            *   "is_popular": 0,
                            *   "can_promotion": 0,
                            *   "status": 1,
                            *   "total_orders": 0,
                            *   "has_options": 0,
                            *   "published_at": "2010-12-30 05:00:00",
                            *   "created_at": "2010-12-30 05:00:00",
                            *   "updated_at": "2010-12-30 05:00:00"
                        *   },
                        *   "product_option": {
                            *   "id": 1,
                            *   "sku": "123456789",
                            *   "barcode": "123456789",
                            *   "price": 10000,
                            *   "price_original": 10000,
                            *   "quantity": 100,
                            *   "stock_address": "100 Lê Văn Sỹ",
                            *   "image_url": "upload/images/option.png",
                            *   "image_mobile_url": "upload/images/option-mb.png",
                            *   "attribute_1": 100,
                            *   "attribute_2": 100,
                            *   "attribute_3": 100
                        *   }
                    *   }
                *   ]
            *   }
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function index(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $user_id      = $request->input('user_id', '');
            $filter = [
                'user_id'      => $user_id,
            ];
            $carts = $this->cartService->getAllPaginate($perPage, $filter);
            $carts->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($carts);
            $pagingArr = $carts->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'carts' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Add product into cart
     * @group Cart management
     * @authenticated
     * @bodyParam product_id integer required Example: 1
     * @bodyParam product_option_id integer Example: 1
     * @bodyParam quantity integer required Example: 1
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
            'product_id'        => 'bail|required|exists:products,id',
            'product_option_id' => 'bail|nullable|exists:product_options,id',
            'quantity'          => 'bail|required|integer|min:1',
        ]);
        try{
            $input = $request->all();

            $result = $this->cartService->create($input);
            if($result['status']){
                return $this->_response($result['data'], $result['message']);
            }else{
                return $this->_error($result['message']);
            }
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Update cart
     * @group Cart management
     * @authenticated
     * @bodyParam is_gift_wrapping boolean Example: 1
     * @bodyParam gift_message string Example: Chuc mung sinh nhat
     * @bodyParam promotion_code string Example: AB01
     * @bodyParam details object[]
     * @bodyParam details[].id integer
     * @bodyParam details[].product_id integer
     * @bodyParam details[].product_option_id integer
     * @bodyParam details[].quantity integer
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
            'is_gift_wrapping'            => 'bail|nullable|boolean',
            'gift_message'                => 'bail|nullable',
            'promotion_code'              => 'bail|nullable|exists:promotions,code',
            'details'                     => 'bail|nullable|array|min:1',
            'details.*.id'                => 'bail|required_unless:details,null|integer|exists:cart_details,id',
            'details.*.product_id'        => 'bail|nullable|exists:products,id',
            'details.*.product_option_id' => 'bail|nullable|exists:product_options,id',
            'details.*.quantity'          => 'bail|required_unless:details,null|integer|min:1',
        ]);
        try{
            $input = $request->all();
            $result = $this->cartService->updateByID($input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data'], $result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Remove cart
     * @group Cart management
     * @authenticated
     * @bodyParam id integer required
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
            'id' => 'required|exists:carts,id',
        ]);
        try{
            $id= $request->input('id');
            $result = $this->cartService->destroyByID($id);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data'], $result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Remove an item in cart
     * @group Cart management
     * @authenticated
     * @bodyParam id integer required
     * @response {
        *   "status": true,
        *   "message": "Deleted"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */

    public function removeDetail(Request $request){
        $this->validate($request, [
            'id' => 'required|exists:cart_details,id',
        ]);
        try{
            $id= $request->input('id');
            $result = $this->cartService->removeDetail($id);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get my cart
     * @group Cart management
     * @authenticated
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "product_id": 1,
                    *   "product_option_id": 1,
                    *   "quantity": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "product": {
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
                        *   "sku": "123456",
                        *   "barcode": "123456",
                        *   "stock_address": "100 Hai Bà Trưng",
                        *   "quantity": 5000,
                        *   "star_rating": 5,
                        *   "title": "Lorem sipum",
                        *   "keyword": "Lorem sipum",
                        *   "description": "Lorem sipum",
                        *   "is_show": 0,
                        *   "is_popular": 0,
                        *   "can_promotion": 0,
                        *   "status": 1,
                        *   "total_orders": 0,
                        *   "has_options": 0,
                        *   "published_at": "2010-12-30 05:00:00",
                        *   "created_at": "2010-12-30 05:00:00",
                        *   "updated_at": "2010-12-30 05:00:00"
                    *   },
                    *   "product_option": {
                            *   "id": 1,
                            *   "sku": "123456789",
                            *   "barcode": "123456789",
                            *   "price": 10000,
                            *   "price_original": 10000,
                            *   "quantity": 100,
                            *   "stock_address": "100 Lê Văn Sỹ",
                            *   "image_url": "upload/images/option.png",
                            *   "image_mobile_url": "upload/images/option-mb.png",
                            *   "attribute_1": 100,
                            *   "attribute_2": 100,
                            *   "attribute_3": 100
                        *   }
                *   }
            * ]
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function getMyCart(Request $request){
        try{
            $carts = $this->cartService->getMyCart();
            return $this->_response($carts);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
