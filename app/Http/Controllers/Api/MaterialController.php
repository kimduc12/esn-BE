<?php
namespace App\Http\Controllers\Api;

use App\Constants\MaterialConst;
use Illuminate\Http\Request;

use App\Services\MaterialService;

class MaterialController extends RestfulController
{
    protected $materialService;
    public function __construct(MaterialService $materialService){
        parent::__construct();
        $this->materialService = $materialService;
    }
    /**
     * Get all materials with paginate
     * @group Material management
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
        *   "materials": [
                *   {
                    *   "id": 1,
                    *   "name": "Samsung",
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
            $filter = [
                'keyword'      => $keyword,
                'status'       => $status
            ];
            $materials = $this->materialService->getAllPaginate($perPage, $filter);
            $materials->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($materials);
            $pagingArr = $materials->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'materials' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a material
     * @group Material management
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
            'name'           => 'bail|required|unique:materials,name',
            'sort'           => 'bail|required|integer',
            'status'         => 'bail|required|in:'.implode(',', MaterialConst::STATUS_VALIDATE)
        ]);
        try{
            $input = $request->all();

            $result = $this->materialService->create($input);
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
     * Get a material by id
     * @group Material management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Samsung",
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
            $result = $this->materialService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a material
     * @group Material management
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
            'name'           => 'bail|required|unique:materials,name,'.$id,
            'sort'           => 'bail|required|integer',
            'status'         => 'bail|required|in:'.implode(',', MaterialConst::STATUS_VALIDATE)
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->materialService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete materials
     * @group Material management
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
            $result = $this->materialService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get materials (only active)
     * @group Material management
     * @queryParam keyword string Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Samsung",
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
            $keyword      = $request->input('keyword', '');
            $filter = [
                'keyword' => $keyword
            ];
            $materials = $this->materialService->getAll($filter);
            return $this->_response($materials);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
