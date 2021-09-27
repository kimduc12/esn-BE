<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Services\AdvicePostService;

class AdvicePostController extends RestfulController
{
    protected $advicePostService;
    public function __construct(AdvicePostService $advicePostService){
        parent::__construct();
        $this->advicePostService = $advicePostService;
    }
    /**
     * Get all advice posts with paginate
     * @group Advice post management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam product_category_ids string Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "posts": [
                *   {
                    *   "id": 1,
                    *   "name": "Tư vấn trẻ sơ sinh",
                    *   "content": "Lorem ipsum",
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
            $product_category_ids = $request->input('product_category_ids', '');
            $filter = [
                'keyword'      => $keyword,
                'product_category_ids' => $product_category_ids,
            ];
            $posts = $this->advicePostService->getAllPaginate($perPage, $filter);
            $posts->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($posts);
            $pagingArr = $posts->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'posts' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create an advice post
     * @group Advice post management
     * @authenticated
     * @bodyParam name string required Example: Tư vấn sơ sinh
     * @bodyParam content string required Example: Lorem ipsum
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
            'name'           => 'bail|required',
            'content'        => 'bail|required',
            'category_ids'   => 'bail|required|array|min:1',
            'category_ids.*' => 'bail|required|exists:product_categories,id',
        ]);
        try{
            $input = $request->all();

            $result = $this->advicePostService->create($input);
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
     * Get an advice post by id
     * @group Advice post management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Tư vấn trẻ sơ sinh",
                *   "content": "Lorem ipsum",
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00",
                *   "categories": [
                        *   {
                            *   "id": 1,
                            *   "name": "Bé trai"
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
            $result = $this->advicePostService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update an advice post
     * @group Advice post management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: Trẻ sơ sinh
     * @bodyParam content string required Example: Lorem ipsum
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
            'name'           => 'bail|required',
            'content'        => 'bail|required',
            'category_ids'   => 'bail|required|array|min:1',
            'category_ids.*' => 'required|exists:product_categories,id',
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->advicePostService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete advice posts
     * @group Advice post management
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
            $result = $this->advicePostService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get an advice post by product category id
     * @group Advice post management
     * @urlParam product_category_id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Tư vấn trẻ sơ sinh",
                *   "content": "lorem ipsum",
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00"
            *   }
        * }
     * @response status=200 scenario="Not found category" {
        *  "status": false,
        *  "message": "Not found"
        * }
     * @response status=200 scenario="Not found any post" {
        *  "status": true,
        *  "data": null
        * }
     */
    public function getAdvicePostByProductCategoryID($product_category_id){
        try{
            $result = $this->advicePostService->getAdvicePostByProductCategoryID($product_category_id);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
