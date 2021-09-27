<?php
namespace App\Http\Controllers\Api;

use App\Constants\BlogCategoryConst;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Services\BlogCategoryService;

class BlogCategoryController extends RestfulController
{
    protected $blogCategoryService;
    public function __construct(BlogCategoryService $blogCategoryService){
        parent::__construct();
        $this->blogCategoryService = $blogCategoryService;
    }
    /**
     * Get all blog categories with paginate
     * @group Blog Category management
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
        *   "categories": [
                *   {
                    *   "id": 1,
                    *   "name": "Blog 1",
                    *   "slug": "blog-1",
                    *   "image_url": "upload/images/blog.png",
                    *   "image_mobile_url": "upload/images/blog-mobile.png",
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "parent_id": 0,
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
            $status            = $request->input('status', '');
            $filter = [
                'keyword'            => $keyword,
                'status'            => $status,
            ];
            $categories = $this->blogCategoryService->getAllPaginate($perPage, $filter);
            $categories->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($categories);
            $pagingArr = $categories->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'categories' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a blog category
     * @group Blog Category management
     * @authenticated
     * @bodyParam name string required Example: blog 1
     * @bodyParam image_url string required Example: /upload/images/blog.png
     * @bodyParam image_mobile_url string Example: /upload/images/blog-mobile.png
     * @bodyParam title string required Example: Lorem sipum
     * @bodyParam keyword string required Example: Lorem sipum
     * @bodyParam description string required Example: Lorem sipum
     * @bodyParam parent_id integer required Example: 0
     * @bodyParam sort integer required Example: 0
     * @bodyParam status integer required Example: 0
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
            'name'             => 'bail|required',
            'image_url'        => 'bail|required',
            'image_mobile_url' => 'bail|nullable',
            'title'            => 'bail|required',
            'keyword'          => 'bail|required',
            'description'      => 'bail|required',
            'parent_id'        => 'bail|required|integer',
            'sort'             => 'bail|required|integer',
            'status'           => 'bail|required|in:'.implode(',', BlogCategoryConst::STATUS_VALIDATE),
        ]);
        try{
            $input = $request->all();

            $result = $this->blogCategoryService->create($input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data'], $result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get a blog category by id
     * @group Blog Category management
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
                *   "title": "Lorem sipum",
                *   "keyword": "Lorem sipum",
                *   "description": "Lorem sipum",
                *   "parent_id": 0,
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
            $result = $this->blogCategoryService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a blog category
     * @group Blog Category management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: blog 1
     * @bodyParam image_url string required Example: /upload/images/blog.png
     * @bodyParam image_mobile_url string Example: /upload/images/blog-mobile.png
     * @bodyParam title string required Example: Lorem sipum
     * @bodyParam keyword string required Example: Lorem sipum
     * @bodyParam description string required Example: Lorem sipum
     * @bodyParam parent_id integer required Example: 0
     * @bodyParam sort integer required Example: 0
     * @bodyParam status integer required Example: 0
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
            'name'             => 'bail|required',
            'image_url'        => 'bail|required',
            'image_mobile_url' => 'bail|nullable',
            'title'            => 'bail|required',
            'keyword'          => 'bail|required',
            'description'      => 'bail|required',
            'parent_id'        => 'bail|required|integer',
            'sort'             => 'bail|required|integer',
            'status'           => 'bail|required|in:'.implode(',', BlogCategoryConst::STATUS_VALIDATE),
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->blogCategoryService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete a blog category
     * @group Blog Category management
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
            $result = $this->blogCategoryService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get blog category (only active)
     * @group Blog Category management
     * @queryParam parent_id integer Field to filter. Defaults to 0
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Blog 1",
                    *   "slug": "blog-1",
                    *   "image_url": "upload/images/blog.png",
                    *   "image_mobile_url": "upload/images/blog-mobile.png",
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "parent_id": 0,
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
    public function getAllActive(Request $request){
        try{
            $parent_id = $request->input('parent_id', 0);
            $filter = [
                'parent_id' => $parent_id,
            ];
            $blogs = $this->blogCategoryService->getAll($filter);
            return $this->_response($blogs);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get blog category detail by slug (only active)
     * @group Blog Category management
     * @urlParam slug string required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Blog 1",
                *   "slug": "blog-1",
                *   "image_url": "upload/images/blog.png",
                *   "image_mobile_url": "upload/images/blog-mobile.png",
                *   "title": "Lorem sipum",
                *   "keyword": "Lorem sipum",
                *   "description": "Lorem sipum",
                *   "parent_id": 0,
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
    public function getBySlug(Request $request, $slug){
        try{
            $result = $this->blogCategoryService->getBySlug($slug);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
