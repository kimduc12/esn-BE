<?php
namespace App\Http\Controllers\Api;

use App\Constants\TopicConst;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Services\TopicService;

class TopicController extends RestfulController
{
    protected $topicService;
    public function __construct(TopicService $topicService){
        parent::__construct();
        $this->topicService = $topicService;
    }
    /**
     * Get all topics with paginate
     * @group Topic management
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
        *   "topics": [
                *   {
                    *   "id": 1,
                    *   "name": "Mua xuan",
                    *   "slug": "mua-xuan",
                    *   "summary": "Mua xuan da den",
                    *   "image_uhd_url": "upload/images/topic.png",
                    *   "image_fhd_url": "upload/images/topic.png",
                    *   "image_hd_url": "upload/images/topic.png",
                    *   "image_mb_url": "upload/images/topic.png",
                    *   "sort": 0,
                    *   "is_active": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "title": "lorem ipsum",
                    *   "keyword": "lorem ipsum",
                    *   "description": "lorem ipsum"
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
            $topics = $this->topicService->getAllPaginate($perPage, $filter);
            $topics->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($topics);
            $pagingArr = $topics->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'topics' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a topic
     * @group Topic management
     * @authenticated
     * @bodyParam name string required Example: Mua xuan
     * @bodyParam summary string Example: Mua xuan da den roi
     * @bodyParam image_uhd_url string Example: upload/images/topic.png
     * @bodyParam image_fhd_url string Example: upload/images/topic.png
     * @bodyParam image_hd_url string Example: upload/images/topic.png
     * @bodyParam image_mb_url string Example: upload/images/topic.png
     * @bodyParam sort integer required Example: 0
     * @bodyParam is_active bool required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam category_ids integer[] required Example: [1,2,3,4]
     * @bodyParam home_product_ids integer[] Example: [1,2,3,4]
     * @bodyParam title string Example: Lorem ipsum
     * @bodyParam keyword string Example: Lorem ipsum
     * @bodyParam description string Example: Lorem ipsum
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
        $request['slug'] = Str::slug($request->name, '-');
        $this->validate($request, [
            'name'               => 'bail|required|max:191',
            'slug'               => 'bail|required|max:191|unique:topics,slug',
            'summary'            => 'bail|nullable',
            'image_uhd_url'      => 'bail|nullable',
            'image_fhd_url'      => 'bail|nullable',
            'image_hd_url'       => 'bail|nullable',
            'image_mb_url'       => 'bail|nullable',
            'sort'               => 'bail|required|integer',
            'is_active'          => 'bail|required|boolean',
            'status'             => 'bail|required|in:'.implode(',', TopicConst::STATUS_VALIDATE),
            'category_ids'       => 'bail|required|array|min:1',
            'category_ids.*'     => 'required|exists:product_categories,id',
            'home_product_ids'   => 'bail|nullable|array|min:1',
            'home_product_ids.*' => 'required|exists:products,id',
            'title'              => 'bail|nullable',
            'keyword'            => 'bail|nullable',
            'description'        => 'bail|nullable',
        ]);
        try{
            $input = $request->all();
            $result = $this->topicService->create($input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data'], $result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get a topic by id
     * @group Topic management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Mua xuan",
                *   "slug": "mua-xuan",
                *   "summary": "Mua xuan da den",
                *   "image_uhd_url": "upload/images/topic.png",
                *   "image_fhd_url": "upload/images/topic.png",
                *   "image_hd_url": "upload/images/topic.png",
                *   "image_mb_url": "upload/images/topic.png",
                *   "sort": 0,
                *   "is_active": 0,
                *   "status": 1,
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00",
                *   "title": "lorem ipsum",
                *   "keyword": "lorem ipsum",
                *   "description": "lorem ipsum",
                *   "categories": [
                        *   {
                            *   "id": 1,
                            *   "name": "Bé trai"
                        *   }
                    *   ],
                *   "home_products": [
                        *   {
                            *   "id": 1,
                            *   "name": "Product"
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
            $result = $this->topicService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a topic
     * @group Topic management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: Mua xuan
     * @bodyParam summary string Example: Mua xuan da den roi
     * @bodyParam image_uhd_url string Example: upload/images/topic.png
     * @bodyParam image_fhd_url string Example: upload/images/topic.png
     * @bodyParam image_hd_url string Example: upload/images/topic.png
     * @bodyParam image_mb_url string Example: upload/images/topic.png
     * @bodyParam sort integer required Example: 0
     * @bodyParam is_active bool required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam category_ids integer[] required Example: [1,2,3,4]
     * @bodyParam home_product_ids integer[] Example: [1,2,3,4]
     * @bodyParam title string Example: Lorem ipsum
     * @bodyParam keyword string Example: Lorem ipsum
     * @bodyParam description string Example: Lorem ipsum
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
        $request['slug'] = Str::slug($request->name, '-');
        $this->validate($request, [
            'name'               => 'bail|required|max:191',
            'slug'               => 'bail|required|max:191|unique:topics,slug,'.$id,
            'summary'            => 'bail|nullable',
            'image_uhd_url'      => 'bail|nullable',
            'image_fhd_url'      => 'bail|nullable',
            'image_hd_url'       => 'bail|nullable',
            'image_mb_url'       => 'bail|nullable',
            'sort'               => 'bail|required|integer',
            'status'             => 'bail|required|in:'.implode(',', TopicConst::STATUS_VALIDATE),
            'category_ids'       => 'bail|required|array|min:1',
            'category_ids.*'     => 'required|exists:product_categories,id',
            'home_product_ids'   => 'bail|nullable|array|min:1',
            'home_product_ids.*' => 'required|exists:products,id',
            'title'              => 'bail|nullable',
            'keyword'            => 'bail|nullable',
            'description'        => 'bail|nullable',
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->topicService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete topics
     * @group Topic management
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
            $result = $this->topicService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get topics with paginate (only can show in FE)
     * @group Topic management
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
        *   "topics": [
                *   {
                    *   "id": 1,
                    *   "name": "Mua xuan",
                    *   "slug": "mua-xuan",
                    *   "summary": "Mua xuan da den",
                    *   "image_uhd_url": "upload/images/topic.png",
                    *   "image_fhd_url": "upload/images/topic.png",
                    *   "image_hd_url": "upload/images/topic.png",
                    *   "image_mb_url": "upload/images/topic.png",
                    *   "sort": 0,
                    *   "is_active": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "title": "lorem ipsum",
                    *   "keyword": "lorem ipsum",
                    *   "description": "lorem ipsum"
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
            $topics = $this->topicService->getListPaginate($perPage, $filter);
            $topics->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($topics);
            $pagingArr = $topics->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'topics' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get topics (only can show in FE)
     * @group Topic management
     * @queryParam category_id integer Field to filter. Defaults to 0
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Mua xuan",
                    *   "slug": "mua-xuan",
                    *   "summary": "Mua xuan da den",
                    *   "image_uhd_url": "upload/images/topic.png",
                    *   "image_fhd_url": "upload/images/topic.png",
                    *   "image_hd_url": "upload/images/topic.png",
                    *   "image_mb_url": "upload/images/topic.png",
                    *   "sort": 0,
                    *   "is_active": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "title": "lorem ipsum",
                    *   "keyword": "lorem ipsum",
                    *   "description": "lorem ipsum"
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
            $topics = $this->topicService->getAll($filter);
            return $this->_response($topics);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get one active topic (only can show and active)
     * @group Topic management
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Mua xuan",
                *   "slug": "mua-xuan",
                *   "summary": "Mua xuan da den",
                *   "image_uhd_url": "upload/images/topic.png",
                *   "image_fhd_url": "upload/images/topic.png",
                *   "image_hd_url": "upload/images/topic.png",
                *   "image_mb_url": "upload/images/topic.png",
                *   "sort": 0,
                *   "is_active": 0,
                *   "status": 1,
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00",
                *   "title": "lorem ipsum",
                *   "keyword": "lorem ipsum",
                *   "description": "lorem ipsum",
                *   "home_products": [
                    *   {
                        *   "id": 1,
                        *   "name": "Product"
                    *   }
                *   ]
            *   }
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function getOneActive(){
        try{
            $topic = $this->topicService->getOneActive();
            return $this->_response($topic);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
