<?php
namespace App\Http\Controllers\Api;

use App\Constants\ProductTypeConst;
use Illuminate\Http\Request;

use App\Services\ProductTypeService;

class ProductTypeController extends RestfulController
{
    protected $productTypeService;
    public function __construct(ProductTypeService $productTypeService){
        parent::__construct();
        $this->productTypeService = $productTypeService;
    }
    /**
     * Get all product types with paginate
     * @group Product type management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam status integer Field to filter. Defaults to null.
     * @queryParam category_ids string Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "types": [
                *   {
                    *   "id": 1,
                    *   "name": "Đầm váy",
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
            $keyword      = $request->input('keyword', '');
            $status       = $request->input('status', '');
            $category_ids = $request->input('category_ids', '');
            $filter = [
                'keyword'      => $keyword,
                'status'       => $status,
                'category_ids' => $category_ids,
            ];
            $types = $this->productTypeService->getAllPaginate($perPage, $filter);
            $types->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($types);
            $pagingArr = $types->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'types' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a product type
     * @group Product type management
     * @authenticated
     * @bodyParam name string required Example: 1 tuoi
     * @bodyParam sort integer required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam category_ids integer[] required Example: [1,2,3,4]
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
            'name'           => 'bail|required|unique:product_types,name',
            'sort'           => 'bail|required|integer',
            'status'         => 'bail|required|in:'.implode(',', ProductTypeConst::STATUS_VALIDATE),
            'category_ids'   => 'bail|required|array|min:1',
            'category_ids.*' => 'bail|required|exists:product_categories,id',
        ]);
        try{
            $input = $request->all();

            $result = $this->productTypeService->create($input);
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
     * Get a product type by id
     * @group Product type management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Đầm váy",
                *   "sort": 0,
                *   "status": 1,
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00",
                *   "categories": [
                        *   {
                            *   "id": 1,
                            *   "name": "Bé trai",
                            *   "slug": "be-trai",
                            *   "image_url": "/upload/images/be-trai.png",
                            *   "image_mobile_url": null,
                            *   "title": "Bé trai",
                            *   "keyword": "Bé trai",
                            *   "description": "Bé trai",
                            *   "parent_id": 0,
                            *   "sort": 0,
                            *   "status": 1,
                            *   "created_at": "2021-01-26T15:09:24.000000Z",
                            *   "updated_at": "2021-01-26T15:09:24.000000Z"
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
            $result = $this->productTypeService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a product type
     * @group Product type management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: 1 tuoi
     * @bodyParam sort integer required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam category_ids integer[] required Example: [1,2,3,4]
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
            'name'           => 'bail|required|unique:product_types,name,'.$id,
            'sort'           => 'bail|required|integer',
            'status'         => 'bail|required|in:'.implode(',', ProductTypeConst::STATUS_VALIDATE),
            'category_ids'   => 'bail|required|array|min:1',
            'category_ids.*' => 'required|exists:product_categories,id',
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->productTypeService->updateByID($id, $input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete product types
     * @group Product type management
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
            $result = $this->productTypeService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get product types with paginate (only active)
     * @group Product type management
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam category_id integer Field to filter. Defaults to 0.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "types": [
                *   {
                    *   "id": 1,
                    *   "name": "Đầm váy",
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
    public function getListPaginate(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $category_id = $request->input('category_id', 0);
            $filter = [
                'category_id' => $category_id,
            ];
            $types = $this->productTypeService->getListPaginate($perPage, $filter);
            $types->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($types);
            $pagingArr = $types->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'types' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get product types (only active)
     * @group Product type management
     * @queryParam category_id integer Field to filter. Defaults to 0
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Đầm váy",
                    *   "sort": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "categories": [
                        *   {
                            *   "id": 1,
                            *   "name": "Bé trai",
                            *   "slug": "be-trai",
                            *   "image_url": "/upload/images/be-trai.png",
                            *   "image_mobile_url": null,
                            *   "title": "Bé trai",
                            *   "keyword": "Bé trai",
                            *   "description": "Bé trai",
                            *   "parent_id": 0,
                            *   "sort": 0,
                            *   "status": 1,
                            *   "created_at": "2021-01-26T15:09:24.000000Z",
                            *   "updated_at": "2021-01-26T15:09:24.000000Z"
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
    public function getAll(Request $request){
        try{
            $category_id = $request->input('category_id', 0);
            $filter = [
                'category_id'       => $category_id,
            ];
            $types = $this->productTypeService->getAll($filter);
            return $this->_response($types);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
