<?php
namespace App\Http\Controllers\Api;

use App\Constants\BlogConst;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Services\BlogService;

class BlogController extends RestfulController
{
    protected $blogService;
    public function __construct(BlogService $blogService){
        parent::__construct();
        $this->blogService = $blogService;
    }
    /**
     * Get all blogs with paginate
     * @group Blog management
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
        *   "blogs": [
                *   {
                    *   "id": 1,
                    *   "name": "Blog 1",
                    *   "slug": "blog-1",
                    *   "image_url": "upload/images/blog.png",
                    *   "image_mobile_url": "upload/images/blog-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_hot": 0,
                    *   "is_top": 0,
                    *   "is_sub_top": 0,
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
    public function index(Request $request){
        try{
            $perPage     = $request->input("per_page", 20) ?: 20;
            $keyword     = $request->input('keyword', '');
            $status      = $request->input('status', '');
            $category_ids = $request->input('category_ids', '');
            $filter = [
                'keyword'     => $keyword,
                'status'      => $status,
                'category_ids' => $category_ids,
            ];
            $blogs = $this->blogService->getAllPaginate($perPage, $filter);
            $blogs->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($blogs);
            $pagingArr = $blogs->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'blogs' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a blog
     * @group Blog management
     * @authenticated
     * @bodyParam name string required Example: blog 1
     * @bodyParam image_url string required Example: /upload/images/blog.png
     * @bodyParam image_mobile_url string Example: /upload/images/blog-mobile.png
     * @bodyParam summary string Example: Lorem sipum
     * @bodyParam content string required Example: Lorem sipum
     * @bodyParam title string Example: Lorem sipum
     * @bodyParam keyword string Example: Lorem sipum
     * @bodyParam description string Example: Lorem sipum
     * @bodyParam is_top bool required Example: 0
     * @bodyParam is_sub_top bool required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam published_at datetime required Example: 2010-10-20 20:00:00
     * @bodyParam category_ids integer[] required Example: [1,2,3,4]
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
            'name'                 => 'bail|required',
            'image_url'            => 'bail|required',
            'image_mobile_url'     => 'bail|nullable',
            'summary'              => 'bail|nullable',
            'content'              => 'bail|required',
            'title'                => 'bail|nullable',
            'keyword'              => 'bail|nullable',
            'description'          => 'bail|nullable',
            'is_top'               => 'bail|required|boolean',
            'is_sub_top'           => 'bail|required|boolean',
            'status'               => 'bail|required|in:'.implode(',', BlogConst::STATUS_VALIDATE),
            'published_at'         => 'bail|required|date_format:Y-m-d H:i:s',
            'category_ids'         => 'bail|required|array|min:1',
            'category_ids.*'       => 'bail|required|exists:blog_categories,id'
        ]);
        try{
            $input = $request->all();

            $result = $this->blogService->create($input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data'], $result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get a blog by id
     * @group Blog management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                    *   "id": 1,
                    *   "name": "Blog 1",
                    *   "slug": "blog-1",
                    *   "image_url": "upload/images/logo.png",
                    *   "image_mobile_url": "upload/images/logo-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_hot": 0,
                    *   "is_top": 0,
                    *   "is_sub_top": 0,
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
            $result = $this->blogService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a blog
     * @group Blog management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: blog 1
     * @bodyParam image_url string required Example: /upload/images/blog.png
     * @bodyParam image_mobile_url string Example: /upload/images/blog-mobile.png
     * @bodyParam summary string Example: Lorem sipum
     * @bodyParam content string required Example: Lorem sipum
     * @bodyParam title string Example: Lorem sipum
     * @bodyParam keyword string Example: Lorem sipum
     * @bodyParam description string Example: Lorem sipum
     * @bodyParam is_top bool required Example: 0
     * @bodyParam is_sub_top bool required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam published_at datetime required Example: 2010-10-20 20:00:00
     * @bodyParam category_ids integer[] required Example: [1,2,3,4]
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
            'name'                 => 'bail|required',
            'image_url'            => 'bail|required',
            'image_mobile_url'     => 'bail|nullable',
            'summary'              => 'bail|required',
            'content'              => 'bail|required',
            'title'                => 'bail|nullable',
            'keyword'              => 'bail|nullable',
            'description'          => 'bail|nullable',
            'is_top'               => 'bail|required|boolean',
            'is_sub_top'           => 'bail|required|boolean',
            'status'               => 'bail|required|in:'.implode(',', BlogConst::STATUS_VALIDATE),
            'published_at'         => 'bail|required',
            'category_ids'         => 'bail|required|array|min:1',
            'category_ids.*'       => 'bail|required|exists:blog_categories,id'
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->blogService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete a blog
     * @group Blog management
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
            $result = $this->blogService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get blogs with paginate (only active)
     * @group Blog management
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
        *   "blogs": [
                *   {
                    *   "id": 1,
                    *   "name": "Blog 1",
                    *   "slug": "blog-1",
                    *   "image_url": "upload/images/blog.png",
                    *   "image_mobile_url": "upload/images/blog-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_hot": 0,
                    *   "is_top": 0,
                    *   "is_sub_top": 0,
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
    public function getListPaginate(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $category_id = $request->input('category_id', 0);
            $filter = [
                'category_id' => $category_id,
                'is_hot' => 0,
                'is_top' => 0,
                'is_sub_top' => 0,
            ];
            $blogs = $this->blogService->getListPaginate($perPage, $filter);
            $blogs->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($blogs);
            $pagingArr = $blogs->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'blogs' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get blogs (only active)
     * @group Blog management
     * @queryParam category_id integer Field to filter. Defaults to 0
     * @queryParam is_hot bool Field to filter. Defaults to 0
     * @queryParam is_top bool Field to filter. Defaults to 0
     * @queryParam is_sub_top bool Field to filter. Defaults to 0
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Blog 1",
                    *   "slug": "blog-1",
                    *   "image_url": "upload/images/blog.png",
                    *   "image_mobile_url": "upload/images/blog-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_hot": 0,
                    *   "is_top": 0,
                    *   "is_sub_top": 0,
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
            $is_hot      = $request->input('is_hot', 0);
            $is_top      = $request->input('is_top', 0);
            $is_sub_top  = $request->input('is_sub_top', 0);
            $filter = [
                'category_id' => $category_id,
                'is_hot'      => $is_hot,
                'is_top'      => $is_top,
                'is_sub_top'  => $is_sub_top,
            ];
            $blogs = $this->blogService->getAll($filter);
            return $this->_response($blogs);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get blog detail by slug (only active)
     * @group Blog management
     * @urlParam slug string required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Blog 1",
                *   "slug": "blog-1",
                *   "image_url": "upload/images/blog.png",
                *   "image_mobile_url": "upload/images/blog-mobile.png",
                *   "summary": "Lorem sipum",
                *   "content": "Lorem sipum",
                *   "title": "Lorem sipum",
                *   "keyword": "Lorem sipum",
                *   "description": "Lorem sipum",
                *   "is_hot": 0,
                *   "is_top": 0,
                *   "is_sub_top": 0,
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
            $result = $this->blogService->getBySlug($slug);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
