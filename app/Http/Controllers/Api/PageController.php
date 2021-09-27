<?php
namespace App\Http\Controllers\Api;

use App\Constants\PageConst;
use Illuminate\Http\Request;

use App\Services\PageService;

class PageController extends RestfulController
{
    protected $pageService;
    public function __construct(PageService $pageService){
        parent::__construct();
        $this->pageService = $pageService;
    }
    /**
     * Get all pages with paginate
     * @group Page management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam type integer Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "pages": [
                *   {
                    *   "id": 1,
                    *   "name": "Hướng dẫn sử dụng",
                    *   "slug": "huong-dan-su-dung",
                    *   "content": "lorem ipsum",
                    *   "title": "lorem ipsum",
                    *   "keyword": "lorem ipsum",
                    *   "description": "lorem ipsum",
                    *   "sort": 0,
                    *   "type": 1,
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
            $keyword = $request->input('keyword', '');
            $type    = $request->input('type', 0);
            $filter = [
                'keyword' => $keyword,
                'type'    => $type,
            ];
            $pages = $this->pageService->getAllPaginate($perPage, $filter);
            $pages->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($pages);
            $pagingArr = $pages->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'pages' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a page
     * @group Page management
     * @authenticated
     * @bodyParam name string required Example: Huong dan su dung
     * @bodyParam content string required Example: lorem ipsum
     * @bodyParam title string Example: lorem ipsum
     * @bodyParam keyword string Example: lorem ipsum
     * @bodyParam description string Example: lorem ipsum
     * @bodyParam type integer required Example: 1
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
            'name'           => 'bail|required|unique:pages,name',
            'content'        => 'bail|required',
            'type'           => 'bail|required|integer|in:'.implode(',', PageConst::TYPE_VALIDATE),
            'sort'           => 'bail|required|integer',
            'status'         => 'bail|required|in:'.implode(',', PageConst::STATUS_VALIDATE)
        ]);
        try{
            $input = $request->all();

            $result = $this->pageService->create($input);
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
     * Get a page by id
     * @group Page management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                    *   "id": 1,
                    *   "name": "Hướng dẫn sử dụng",
                    *   "slug": "huong-dan-su-dung",
                    *   "content": "lorem ipsum",
                    *   "title": "lorem ipsum",
                    *   "keyword": "lorem ipsum",
                    *   "description": "lorem ipsum",
                    *   "sort": 0,
                    *   "type": 1,
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
            $result = $this->pageService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a age
     * @group Page management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: Huong dan su dung
     * @bodyParam content string required Example: lorem ipsum
     * @bodyParam title string Example: lorem ipsum
     * @bodyParam keyword string Example: lorem ipsum
     * @bodyParam description string Example: lorem ipsum
     * @bodyParam type integer required Example: 1
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
            'name'           => 'bail|required|unique:pages,name',
            'content'        => 'bail|required',
            'type'           => 'bail|required|integer|in:'.implode(',', PageConst::TYPE_VALIDATE),
            'sort'           => 'bail|required|integer',
            'status'         => 'bail|required|in:'.implode(',', PageConst::STATUS_VALIDATE)
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->pageService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete pages
     * @group Page management
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
            $result = $this->pageService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get pages with paginate (only active)
     * @group Page management
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam type integer Field to filter. Defaults to 0.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "pages": [
                *   {
                    *   "id": 1,
                    *   "name": "Hướng dẫn sử dụng",
                    *   "slug": "huong-dan-su-dung",
                    *   "content": "lorem ipsum",
                    *   "title": "lorem ipsum",
                    *   "keyword": "lorem ipsum",
                    *   "description": "lorem ipsum",
                    *   "sort": 0,
                    *   "type": 1,
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
            $type = $request->input('type', 0);
            $filter = [
                'type' => $type,
            ];
            $pages = $this->pageService->getListPaginate($perPage, $filter);
            $pages->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($pages);
            $pagingArr = $pages->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'pages' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get pages (only active)
     * @group Page management
     * @queryParam type integer Field to filter. Defaults to 0
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Hướng dẫn sử dụng",
                    *   "slug": "huong-dan-su-dung",
                    *   "content": "lorem ipsum",
                    *   "title": "lorem ipsum",
                    *   "keyword": "lorem ipsum",
                    *   "description": "lorem ipsum",
                    *   "sort": 0,
                    *   "type": 1,
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
            $type = $request->input('type', 0);
            $filter = [
                'type'       => $type,
            ];
            $ages = $this->pageService->getAll($filter);
            return $this->_response($ages);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get page detail by slug (only active)
     * @group Page management
     * @urlParam slug string required
     * @response {
        *   "status": true,
        *   "data": {
                    *   "id": 1,
                    *   "name": "Hướng dẫn sử dụng",
                    *   "slug": "huong-dan-su-dung",
                    *   "content": "lorem ipsum",
                    *   "title": "lorem ipsum",
                    *   "keyword": "lorem ipsum",
                    *   "description": "lorem ipsum",
                    *   "sort": 0,
                    *   "type": 1,
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
            $result = $this->pageService->getBySlug($slug);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
