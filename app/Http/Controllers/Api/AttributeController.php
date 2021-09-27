<?php
namespace App\Http\Controllers\Api;

use App\Constants\AttributeConst;
use Illuminate\Http\Request;

use App\Services\AttributeService;

class AttributeController extends RestfulController
{
    protected $attributeService;
    public function __construct(AttributeService $attributeService){
        parent::__construct();
        $this->attributeService = $attributeService;
    }

    /**
     * Get all attributes
     * @group Attribute management
     * @authenticated
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "slug": "size",
                    *   "name": "Product size",
                    *   "description": null,
                    *   "sort_order": 1,
                    *   "group": null,
                    *   "type": "varchar",
                    *   "is_required": false,
                    *   "is_collection": false,
                    *   "default": null,
                    *   "created_at": "2021-02-10T06:23:11.000000Z",
                    *   "updated_at": "2021-02-10T06:23:11.000000Z"
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
            $ages = $this->attributeService->getAll();
            return $this->_response($ages);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }


    /**
     * Create a attribute
     * @group Attribute management
     * @authenticated
     * @bodyParam name string required Example: Product Size
     * @bodyParam slug string required Example: size
     * @bodyParam type string required Example: varchar
     * @bodyParam entities string[] required Example: ['ProductOption']
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
            'name'       => 'bail|required',
            'slug'       => 'bail|required|unique:attributes,slug',
            'type'       => 'bail|required|in:'.implode(',', AttributeConst::TYPES_ALLOW),
            'entities'   => 'bail|required|array|min:1',
            'entities.*' => 'bail|required|in:'.implode(',', AttributeConst::ENTITIES_ALLOW),
        ]);
        try{
            $input = $request->all();

            $result = $this->attributeService->create($input);
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
     * Get an attribute by id
     * @group Attribute management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data":
                *   {
                    *   "id": 1,
                    *   "slug": "size",
                    *   "name": "Product size",
                    *   "description": null,
                    *   "sort_order": 1,
                    *   "group": null,
                    *   "type": "varchar",
                    *   "is_required": false,
                    *   "is_collection": false,
                    *   "default": null,
                    *   "created_at": "2021-02-10T06:23:11.000000Z",
                    *   "updated_at": "2021-02-10T06:23:11.000000Z"
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
            $result = $this->attributeService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update an attribute
     * @group Attribute management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: Product Size
     * @bodyParam slug string required Example: size
     * @bodyParam type string required Example: varchar
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
            'name'       => 'bail|required',
            'slug'       => 'bail|required|unique:attributes,slug,'.$id,
            'type'       => 'bail|required|in:'.implode(',', AttributeConst::TYPES_ALLOW)
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->attributeService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete attributes
     * @group Attribute management
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
            $result = $this->attributeService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get all attributes by one entity
     * @group Attribute management
     * @urlParam entity string required
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "slug": "size",
                    *   "name": "Product size",
                    *   "description": null,
                    *   "sort_order": 1,
                    *   "group": null,
                    *   "type": "varchar",
                    *   "is_required": false,
                    *   "is_collection": false,
                    *   "default": null,
                    *   "created_at": "2021-02-10T06:23:11.000000Z",
                    *   "updated_at": "2021-02-10T06:23:11.000000Z",
                    *   "data": [
                        *   {
                            *   "id": 1,
                            *   "content": "32"
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
    public function getByEntity($entity){
        try{
            $attributes = $this->attributeService->getByEntity($entity);
            return $this->_response($attributes);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get all attributes by entity and entity ID
     * @group Attribute management
     * @authenticated
     * @urlParam entity string required
     * @urlParam entity_id integer required
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "slug": "size",
                    *   "name": "Product size",
                    *   "description": null,
                    *   "sort_order": 1,
                    *   "group": null,
                    *   "type": "varchar",
                    *   "is_required": false,
                    *   "is_collection": false,
                    *   "default": null,
                    *   "created_at": "2021-02-10T06:23:11.000000Z",
                    *   "updated_at": "2021-02-10T06:23:11.000000Z",
                    *   "data": [
                        *   {
                            *   "id": 1,
                            *   "content": "32"
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
    public function getByEntityAndEntityID($entity, $entity_id){
        try{
            $attributes = $this->attributeService->getByEntityAndEntityID($entity, $entity_id);
            return $this->_response($attributes);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
