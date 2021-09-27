<?php
namespace App\Http\Controllers\Api;

use App\Constants\PatternConst;
use Illuminate\Http\Request;

use App\Services\PatternService;

class PatternController extends RestfulController
{
    protected $patternService;
    public function __construct(PatternService $patternService){
        parent::__construct();
        $this->patternService = $patternService;
    }
    /**
     * Get all patterns with paginate
     * @group Pattern management
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
        *   "patterns": [
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
            $patterns = $this->patternService->getAllPaginate($perPage, $filter);
            $patterns->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($patterns);
            $pagingArr = $patterns->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'patterns' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a pattern
     * @group Pattern management
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
            'name'           => 'bail|required|unique:patterns,name',
            'sort'           => 'bail|required|integer',
            'status'         => 'bail|required|in:'.implode(',', PatternConst::STATUS_VALIDATE)
        ]);
        try{
            $input = $request->all();

            $result = $this->patternService->create($input);
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
     * Get a pattern by id
     * @group Pattern management
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
            $result = $this->patternService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a pattern
     * @group Pattern management
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
            'name'           => 'bail|required|unique:patterns,name,'.$id,
            'sort'           => 'bail|required|integer',
            'status'         => 'bail|required|in:'.implode(',', PatternConst::STATUS_VALIDATE)
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->patternService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete patterns
     * @group Pattern management
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
            $result = $this->patternService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get patterns (only active)
     * @group Pattern management
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
            $patterns = $this->patternService->getAll($filter);
            return $this->_response($patterns);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
