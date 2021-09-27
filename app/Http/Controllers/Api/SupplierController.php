<?php
namespace App\Http\Controllers\Api;

use App\Constants\SupplierConst;
use Illuminate\Http\Request;

use App\Services\SupplierService;

class SupplierController extends RestfulController
{
    protected $supplierService;
    public function __construct(SupplierService $supplierService){
        parent::__construct();
        $this->supplierService = $supplierService;
    }
    /**
     * Get all suppliers with paginate
     * @group Supplier management
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
        *   "suppliers": [
                *   {
                    *   "id": 1,
                    *   "name": "Phong Vu",
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
            $suppliers = $this->supplierService->getAllPaginate($perPage, $filter);
            $suppliers->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($suppliers);
            $pagingArr = $suppliers->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'suppliers' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a supplier
     * @group Supplier management
     * @authenticated
     * @bodyParam name string required Example: 1 tuoi
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
            'name'           => 'bail|required|unique:suppliers,name',
            'sort'           => 'bail|required|integer',
            'status'         => 'bail|required|in:'.implode(',', SupplierConst::STATUS_VALIDATE),
        ]);
        try{
            $input = $request->all();

            $result = $this->supplierService->create($input);
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
     * Get a supplier by id
     * @group Supplier management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Phong Vu",
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
            $result = $this->supplierService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a supplier
     * @group Supplier management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: 1 tuoi
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
            'name'           => 'bail|required|unique:suppliers,name,'.$id,
            'sort'           => 'bail|required|integer',
            'status'         => 'bail|required|in:'.implode(',', SupplierConst::STATUS_VALIDATE)
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->supplierService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete suppliers
     * @group Supplier management
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
            $result = $this->supplierService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get suppliers with paginate (only active)
     * @group Supplier management
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
        *   "suppliers": [
                *   {
                    *   "id": 1,
                    *   "name": "Phong Vu",
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
            $suppliers = $this->supplierService->getListPaginate($perPage, $filter);
            $suppliers->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($suppliers);
            $pagingArr = $suppliers->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'suppliers' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get suppliers (only active)
     * @group Supplier management
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Phong Vu",
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
    public function getAll(Request $request){
        try{
            //$category_id = $request->input('category_id', 0);
            $filter = [

            ];
            $suppliers = $this->supplierService->getAll($filter);
            return $this->_response($suppliers);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
