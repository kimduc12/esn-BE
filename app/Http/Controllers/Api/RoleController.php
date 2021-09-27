<?php
namespace App\Http\Controllers\Api;

use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends RestfulController
{
    protected $roleService;
    public function __construct(RoleService $roleService){
        parent::__construct();
        $this->roleService = $roleService;
    }

    /**
     * Get all roles with paginate
     * @group Role management
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
        *   "roles": {
                *   "id": 1,
                *   "name": "Admin",
                *   "permissions": [
                        *   {
                            *   "id": 1,
                            *   "name": "role.list",
                            *   "guard_name": "web",
                            *   "created_at": "2021-01-24T12:18:27.000000Z",
                            *   "updated_at": "2021-01-24T12:18:27.000000Z"
                        *   }
                    *   ]
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
            $keyword            = $request->input('keyword', '');
            $filter = [
                'keyword'            => $keyword,
            ];
            $roles = $this->roleService->getAllPaginate($perPage, $filter);

            $roles->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($roles);
            $pagingArr = $roles->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'roles' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a role
     * @group Role management
     * @authenticated
     * @bodyParam name string required Example: Admin
     * @bodyParam permission_ids integer[] required
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
            'name'             => 'bail|required',
            'permission_ids'   => 'bail|required|array',
            'permission_ids.*' => 'required|exists:permissions,id',
        ]);
        try{
            $input = $request->all();

            $result = $this->roleService->create($input);
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
     * Get a role by id
     * @group Role management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Admin",
                *   "permissions": [
                        *   {
                            *   "id": 1,
                            *   "name": "role.list",
                            *   "guard_name": "web",
                            *   "created_at": "2021-01-24T12:18:27.000000Z",
                            *   "updated_at": "2021-01-24T12:18:27.000000Z"
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
            $result = $this->roleService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a role
     * @group Role management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: Admin
     * @bodyParam permission_ids integer[] required
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
            'name'             => 'bail|required',
            'permission_ids'   => 'bail|required|array',
            'permission_ids.*' => 'required|exists:permissions,id',
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->roleService->updateByID($id, $input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete a role
     * @group Role management
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
            $result = $this->roleService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get all roles
     * @group Role management
     * @authenticated
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Admin",
                *   "permissions": [
                        *   {
                            *   "id": 1,
                            *   "name": "role.list",
                            *   "guard_name": "web",
                            *   "created_at": "2021-01-24T12:18:27.000000Z",
                            *   "updated_at": "2021-01-24T12:18:27.000000Z"
                        *   }
                    *   ]
            * }
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function getAll(){
        try{
            $roles = $this->roleService->getAll();
            return $this->_response($roles);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
