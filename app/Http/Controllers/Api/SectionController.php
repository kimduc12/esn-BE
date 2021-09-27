<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Services\SectionService;
use App\Constants\SectionConst;

class SectionController extends RestfulController
{
    protected $sectionService;
    public function __construct(SectionService $sectionService){
        parent::__construct();
        $this->sectionService = $sectionService;
    }
    /**
     * Get all sections with paginate
     * @group Section management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam position integer Field to filter. Defaults to null.
     * @queryParam type integer Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "sections": [
                *   {
                    *   "id": 1,
                    *   "name": "Sản phẩm khuyến mãi",
                    *   "url": "san-pham-khuyen-mai",
                    *   "position": 0,
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
            $keyword  = $request->input('keyword', '');
            $position = $request->input('position', '');
            $type     = $request->input('type', '');
            $filter = [
                'keyword'  => $keyword,
                'position' => $position,
                'type'     => $type,
            ];
            $sections = $this->sectionService->getAllPaginate($perPage, $filter);
            $sections->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($sections);
            $pagingArr = $sections->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'sections' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a section
     * @group Section management
     * @authenticated
     * @bodyParam name string required Example: Sản phẩm khuyến mãi
     * @bodyParam url string Example: san-pham-khuyen-mai
     * @bodyParam position integer required Example: 0
     * @bodyParam type integer required Example: 1
     * @bodyParam product_ids integer[] Example: [1,2,3,4]
     * @bodyParam blog_ids integer[] Example: [1,2,3,4]
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
            'name'          => 'bail|required',
            'position'      => 'bail|required|integer',
            'type'          => 'bail|required|integer',
            'product_ids'   => 'bail|nullable|array|min:1',
            'product_ids.*' => 'bail|nullable|exists:products,id',
            'blog_ids'      => 'bail|nullable|array|min:1',
            'blog_ids.*'    => 'bail|nullable|exists:blogs,id',
            'status'        => 'bail|required|in:'.implode(',', SectionConst::STATUS_VALIDATE),
        ]);
        try{
            $input = $request->all();

            $result = $this->sectionService->create($input);
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
     * Get a section by id
     * @group Section management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Sản phẩm khuyến mãi",
                *   "url": "san-pham-khuyen-mai",
                *   "position": 0,
                *   "type": 1,
                *   "status": 1,
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00",
                *   "products": [
                        *   {
                            *   "id": 1,
                            *   "sku": "HBN-1",
                            *   "name": "product 1",
                            *   "slug": "product-1",
                            *   "image_url": "upload/images/product.png",
                            *   "image_mobile_url": "upload/images/product-mobile.png",
                            *   "summary": "Lorem sipum",
                            *   "content": "Lorem sipum"
                        *   }
                    *   ],
                *   "blogs": [
                        *   {
                            *   "id": 1,
                            *   "name": "Blog 1"
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
            $result = $this->sectionService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Update a section
     * @group Section management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: Sản phẩm khuyến mãi
     * @bodyParam url string Example: san-pham-khuyen-mai
     * @bodyParam position integer required Example: 0
     * @bodyParam type integer required Example: 1
     * @bodyParam product_ids integer[] Example: [1,2,3,4]
     * @bodyParam blog_ids integer[] Example: [1,2,3,4]
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
            'name'          => 'bail|required',
            'position'      => 'bail|required|integer',
            'type'          => 'bail|required|integer',
            'product_ids'   => 'bail|nullable|array|min:1',
            'product_ids.*' => 'bail|nullable|exists:products,id',
            'blog_ids'      => 'bail|nullable|array|min:1',
            'blog_ids.*'    => 'bail|nullable|exists:blogs,id',
            'status'        => 'bail|required|in:'.implode(',', SectionConst::STATUS_VALIDATE),
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->sectionService->updateByID($id, $input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Delete sections
     * @group Section management
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
            $result = $this->sectionService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }


    /**
     * Get all items in a section by position (only active)
     * @group Section management
     * @urlParam position integer required
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Sản phẩm khuyến mãi",
                    *   "url": "san-pham-khuyen-mai",
                    *   "position": 0,
                    *   "type": 1,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "items": [
                        *   {
                            *   "id": 1,
                            *   "sku": "HBN-1",
                            *   "name": "product 1",
                            *   "slug": "product-1",
                            *   "image_url": "upload/images/product.png",
                            *   "image_mobile_url": "upload/images/product-mobile.png",
                            *   "summary": "Lorem sipum",
                            *   "content": "Lorem sipum"
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
    public function getOneByPosition($position){
        try{
            $result = $this->sectionService->getOneByPosition($position);
            if($result['status'] == false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
