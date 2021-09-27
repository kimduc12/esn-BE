<?php
namespace App\Http\Controllers\Api;

use App\Constants\ProductCategoryConst;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Services\ProductCategoryService;

class ProductCategoryController extends RestfulController
{
    protected $productCategoryService;
    public function __construct(ProductCategoryService $productCategoryService){
        parent::__construct();
        $this->productCategoryService = $productCategoryService;
    }
    /**
     * Get all product categories with paginate
     * @group Product Category management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam status integer Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "categories": [
                *   {
                    *   "id": 1,
                    *   "name": "product 1",
                    *   "slug": "product-1",
                    *   "image_url": "upload/images/product.png",
                    *   "image_mobile_url": "upload/images/product-mobile.png",
                    *   "image_icon_url": "home",
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "parent_id": 0,
                    *   "sort": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "childrenRecursive": [{
                        *   "id": 1,
                        *   "name": "product 1-1",
                        *   "slug": "product-1-1",
                        *   "image_url": "upload/images/product.png",
                        *   "image_mobile_url": "upload/images/product-mobile.png",
                        *   "title": "Lorem sipum",
                        *   "keyword": "Lorem sipum",
                        *   "description": "Lorem sipum",
                        *   "parent_id": 1,
                        *   "sort": 0,
                        *   "status": 1,
                        *   "created_at": "2010-12-30 05:00:00",
                        *   "updated_at": "2010-12-30 05:00:00"
                    *   }],
                    *   "product_types": [
                        *   {
                            *   "id": 1,
                            *   "name": "Đầm"
                        *   }
                    *   ],
                    *   "topics": [
                        *   {
                            *   "id": 1,
                            *   "name": "Sinh nhật"
                        *   }
                    *   ],
                    *   "ages": [
                        *   {
                            *   "id": 1,
                            *   "name": "Sơ sinh"
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
    public function index(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $keyword = $request->input('keyword', '');
            $status = $request->input('status', '');
            $filter = [
                'keyword'            => $keyword,
                'status'            => $status,
            ];
            $categories = $this->productCategoryService->getAllPaginate($perPage, $filter);
            $categories->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($categories);
            $pagingArr = $categories->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'categories' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a product category
     * @group Product Category management
     * @authenticated
     * @bodyParam name string required Example: product 1
     * @bodyParam image_url string Example: /upload/images/product.png
     * @bodyParam image_mobile_url string Example: /upload/images/product-mobile.png
     * @bodyParam image_icon_url string Example: home
     * @bodyParam title string Example: Lorem sipum
     * @bodyParam keyword string Example: Lorem sipum
     * @bodyParam description string Example: Lorem sipum
     * @bodyParam parent_id integer required Example: 0
     * @bodyParam sort integer required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam product_type_ids integer[] Example: [1,2,3,4]
     * @bodyParam topic_ids integer[] Example: [1,2,3,4]
     * @bodyParam age_ids integer[] Example: [1,2,3,4]
     * @response {
        *   "status": true,
        *   "message": "Created"
        * }
     * @response status=200 scenario="Duplicate name by unique slug" {
        *  "status": false,
        *  "message": "Tiêu đề đã được sử dụng"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function store(Request $request){
        $this->validate($request, [
            'name'               => 'bail|required',
            'image_url'          => 'bail|nullable',
            'image_mobile_url'   => 'bail|nullable',
            'title'              => 'bail|nullable',
            'keyword'            => 'bail|nullable',
            'description'        => 'bail|nullable',
            'parent_id'          => 'bail|required|integer',
            'sort'               => 'bail|required|integer',
            'status'             => 'bail|required|in:'.implode(',', ProductCategoryConst::STATUS_VALIDATE),
            'product_type_ids'   => 'bail|nullable|array|min:1',
            'product_type_ids.*' => 'bail|nullable|exists:product_types,id',
            'topic_ids'          => 'bail|nullable|array|min:1',
            'topic_ids.*'        => 'bail|nullable|exists:topics,id',
            'age_ids'            => 'bail|nullable|array|min:1',
            'age_ids.*'          => 'bail|nullable|exists:ages,id',
        ]);
        try{
            $input = $request->all();

            $result = $this->productCategoryService->create($input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data'], $result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get a product category by id
     * @group Product Category management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "product 1",
                *   "slug": "product-1",
                *   "image_url": "upload/images/logo.png",
                *   "image_mobile_url": "upload/images/logo-mobile.png",
                *   "image_icon_url": "home",
                *   "title": "Lorem sipum",
                *   "keyword": "Lorem sipum",
                *   "description": "Lorem sipum",
                *   "parent_id": 0,
                *   "sort": 0,
                *   "status": 1,
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00",
                *   "childrenRecursive": [{
                    *   "id": 1,
                    *   "name": "product 1-1",
                    *   "slug": "product-1-1",
                    *   "image_url": "upload/images/product.png",
                    *   "image_mobile_url": "upload/images/product-mobile.png",
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "parent_id": 1,
                    *   "sort": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00"
                *   }],
                *   "product_types": [
                    *   {
                        *   "id": 1,
                        *   "name": "Đầm"
                    *   }
                *   ],
                *   "topics": [
                    *   {
                        *   "id": 1,
                        *   "name": "Sinh nhật"
                    *   }
                *   ],
                *   "ages": [
                    *   {
                        *   "id": 1,
                        *   "name": "Sơ sinh"
                    *   }
                *   ]
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
            $result = $this->productCategoryService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a product category
     * @group Product Category management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: product 1
     * @bodyParam image_url string Example: /upload/images/product.png
     * @bodyParam image_mobile_url string Example: /upload/images/product-mobile.png
     * @bodyParam image_icon_url string Example: home
     * @bodyParam title string Example: Lorem sipum
     * @bodyParam keyword string Example: Lorem sipum
     * @bodyParam description string Example: Lorem sipum
     * @bodyParam parent_id integer required Example: 0
     * @bodyParam sort integer required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam product_type_ids integer[] Example: [1,2,3,4]
     * @bodyParam topic_ids integer[] Example: [1,2,3,4]
     * @bodyParam age_ids integer[] Example: [1,2,3,4]
     * @response {
        *   "status": true,
        *   "message": "Updated"
        * }
     * @response status=200 scenario="Duplicate name by unique slug" {
        *  "status": false,
        *  "message": "Tiêu đề đã được sử dụng"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function update(Request $request, $id){
        $this->validate($request, [
            'name'               => 'bail|required',
            'image_url'          => 'bail|nullable',
            'image_mobile_url'   => 'bail|nullable',
            'title'              => 'bail|nullable',
            'keyword'            => 'bail|nullable',
            'description'        => 'bail|nullable',
            'parent_id'          => 'bail|required|integer',
            'sort'               => 'bail|required|integer',
            'status'             => 'bail|required|in:'.implode(',', ProductCategoryConst::STATUS_VALIDATE),
            'product_type_ids'   => 'bail|nullable|array|min:1',
            'product_type_ids.*' => 'bail|nullable|exists:product_types,id',
            'topic_ids'          => 'bail|nullable|array|min:1',
            'topic_ids.*'        => 'bail|nullable|exists:topics,id',
            'age_ids'            => 'bail|nullable|array|min:1',
            'age_ids.*'          => 'bail|nullable|exists:ages,id',
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->productCategoryService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete a product category
     * @group Product Category management
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
            $result = $this->productCategoryService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get product category (only active)
     * @group Product Category management
     * @queryParam parent_id integer Field to filter. Defaults to 0
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "product 1",
                    *   "slug": "product-1",
                    *   "image_url": "upload/images/product.png",
                    *   "image_mobile_url": "upload/images/product-mobile.png",
                    *   "image_icon_url": "home",
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "parent_id": 0,
                    *   "sort": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "childrenRecursive": [{
                        *   "id": 1,
                        *   "name": "product 1-1",
                        *   "slug": "product-1-1",
                        *   "image_url": "upload/images/product.png",
                        *   "image_mobile_url": "upload/images/product-mobile.png",
                        *   "title": "Lorem sipum",
                        *   "keyword": "Lorem sipum",
                        *   "description": "Lorem sipum",
                        *   "parent_id": 1,
                        *   "sort": 0,
                        *   "status": 1,
                        *   "created_at": "2010-12-30 05:00:00",
                        *   "updated_at": "2010-12-30 05:00:00"
                    *   }]
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
            $parent_id = $request->input('parent_id', 0);
            $filter = [
                'parent_id' => $parent_id,
            ];
            $products = $this->productCategoryService->getAll($filter);
            return $this->_response($products);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get product category detail by slug (only active)
     * @group Product Category management
     * @urlParam slug string required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "product 1",
                *   "slug": "product-1",
                *   "image_url": "upload/images/product.png",
                *   "image_mobile_url": "upload/images/product-mobile.png",
                *   "image_icon_url": "home",
                *   "title": "Lorem sipum",
                *   "keyword": "Lorem sipum",
                *   "description": "Lorem sipum",
                *   "parent_id": 0,
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
    public function getBySlug(Request $request, $slug){
        try{
            $result = $this->productCategoryService->getBySlug($slug);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
