<?php
namespace App\Http\Controllers\Api;

use App\Constants\PromotionConst;
use Illuminate\Http\Request;

use App\Services\PromotionService;

class PromotionController extends RestfulController
{
    protected $promotionService;
    public function __construct(PromotionService $promotionService){
        parent::__construct();
        $this->promotionService = $promotionService;
    }
    /**
     * Get all promotions with paginate
     * @group Promotion management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam group_type integer Field to filter. Defaults to null.
     * @queryParam created_by integer Field to filter. Defaults to null.
     * @queryParam is_active integer Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "promotions": [
                *   {
                    *   "name": "Giảm mạnh nhờ covid",
                    *   "code": "ABC001",
                    *   "is_common_use": 0,
                    *   "group_type": 1,
                    *   "type": 1,
                    *   "discount_amount": 10000,
                    *   "total_used_amount": 0,
                    *   "limited_amount": 10,
                    *   "is_never_limited": 0,
                    *   "start_datetime": "2012-10-20 00:00:00",
                    *   "end_datetime": "2012-10-20 00:00:00",
                    *   "is_never_expired": 0,
                    *   "apply_type_1": 1,
                    *   "apply_value_1": 1,
                    *   "apply_type_2": 1,
                    *   "apply_value_2": 1,
                    *   "created_by": 1,
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
            $keyword    = $request->input('keyword', '');
            $group_type = $request->input('group_type', '');
            $created_by = $request->input('created_by', '');
            $is_active  = $request->input('is_active', '');
            $filter = [
                'keyword'    => $keyword,
                'group_type' => $group_type,
                'created_by' => $created_by,
                'is_active'  => $is_active,
            ];
            $promotions = $this->promotionService->getAllPaginate($perPage, $filter);
            $promotions->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($promotions);
            $pagingArr = $promotions->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'promotions' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a code promotion
     * @group Promotion management
     * @authenticated
     * @bodyParam code string required Example: 0
     * @bodyParam is_common_use boolean required Example: 0
     * @bodyParam type integer required Example: 0
     * @bodyParam discount_amount integer required Example: 0
     * @bodyParam apply_type_1 integer required Example: 0
     * @bodyParam apply_value_1 integer Example: 0
     * @bodyParam apply_array_value_1 integer[] Example: [1,2,3,4]
     * @bodyParam limited_amount integer Example: 0
     * @bodyParam is_never_limited boolean required Example: 0
     * @bodyParam start_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam end_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam is_never_expired boolean required Example: 0
     * @response {
        *   "status": true,
        *   "message": "Created"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function storeCode(Request $request){
        $this->validate($request, [
            'code'                  => 'bail|required|unique:promotions,code',
            'is_common_use'         => 'bail|required|boolean',
            'type'                  => 'bail|required|integer|in:'.implode(',', PromotionConst::TYPE_VALIDATE),
            'discount_amount'       => 'bail|required|integer|min:1',
            'apply_type_1'          => 'bail|required|integer|in:'.implode(',', PromotionConst::APPLY_TYPE_VALIDATE),
            'apply_value_1'         => 'bail|required|integer',
            'apply_array_value_1'   => 'bail|nullable|array|min:1',
            'apply_array_value_1.*' => 'bail|nullable|integer',
            'limited_amount'        => 'bail|nullable|integer|min:1',
            'is_never_limited'      => 'bail|required|boolean',
            'start_datetime'        => 'bail|nullable|date_format:Y-m-d H:i:s',
            'end_datetime'          => 'bail|nullable|date_format:Y-m-d H:i:s',
            'is_never_expired'      => 'bail|required|boolean',
        ]);
        try{
            $input = $request->all();

            $result = $this->promotionService->createCode($input);
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
     * Create a program promotion
     * @group Promotion management
     * @authenticated
     * @bodyParam name string required Example: 0
     * @bodyParam type integer required Example: 0
     * @bodyParam discount_amount integer required Example: 0
     * @bodyParam apply_type_1 integer required Example: 0
     * @bodyParam apply_value_1 integer Example: 0
     * @bodyParam apply_array_value_1 integer[] Example: [1,2,3,4]
     * @bodyParam apply_type_2 integer required Example: 0
     * @bodyParam apply_value_2 integer Example: 0
     * @bodyParam start_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam end_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam is_never_expired boolean required Example: 0
     * @response {
        *   "status": true,
        *   "message": "Created"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function storeProgram(Request $request){
        $this->validate($request, [
            'name'                  => 'bail|required',
            'type'                  => 'bail|required|integer|in:'.implode(',', PromotionConst::TYPE_VALIDATE),
            'discount_amount'       => 'bail|required|integer|min:1',
            'apply_type_1'          => 'bail|required|integer|in:'.implode(',', PromotionConst::APPLY_TYPE_VALIDATE),
            'apply_value_1'         => 'bail|required|integer',
            'apply_array_value_1'   => 'bail|nullable|array|min:1',
            'apply_array_value_1.*' => 'bail|nullable|integer',
            'apply_type_2'          => 'bail|required|integer|in:'.implode(',', PromotionConst::APPLY_TYPE_2_VALIDATE),
            'apply_value_2'         => 'bail|required|integer',
            'start_datetime'        => 'bail|nullable|date_format:Y-m-d H:i:s',
            'end_datetime'          => 'bail|nullable|date_format:Y-m-d H:i:s',
            'is_never_expired'      => 'bail|required|boolean',
        ]);
        try{
            $input = $request->all();

            $result = $this->promotionService->createProgram($input);
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
     * Get a promotion by id
     * @group Promotion management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "name": "Giảm mạnh nhờ covid",
                *   "code": "ABC001",
                *   "is_common_use": 0,
                *   "group_type": 1,
                *   "type": 1,
                *   "discount_amount": 10000,
                *   "total_used_amount": 0,
                *   "limited_amount": 10,
                *   "is_never_limited": 0,
                *   "start_datetime": "2012-10-20 00:00:00",
                *   "end_datetime": "2012-10-20 00:00:00",
                *   "is_never_expired": 0,
                *   "apply_type_1": 1,
                *   "apply_value_1": 1,
                *   "apply_type_2": 1,
                *   "apply_value_2": 1,
                *   "created_by": 1,
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
            $result = $this->promotionService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Update a code promotion
     * @group Promotion management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam code string required Example: 0
     * @bodyParam is_common_use boolean required Example: 0
     * @bodyParam type integer required Example: 0
     * @bodyParam discount_amount integer required Example: 0
     * @bodyParam apply_type_1 integer required Example: 0
     * @bodyParam apply_value_1 integer Example: 0
     * @bodyParam limited_amount integer Example: 0
     * @bodyParam is_never_limited boolean required Example: 0
     * @bodyParam start_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam end_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam is_never_expired boolean required Example: 0
     * @response {
        *   "status": true,
        *   "message": "Updated"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function updateCode(Request $request, $id){
        $this->validate($request, [
            'code'             => 'bail|required|unique:promotions,code,'.$id,
            'is_common_use'    => 'bail|required|boolean',
            'type'             => 'bail|required|integer|in:'.implode(',', PromotionConst::TYPE_VALIDATE),
            'discount_amount'  => 'bail|required|integer|min:1',
            'apply_type_1'     => 'bail|required|integer|in:'.implode(',', PromotionConst::APPLY_TYPE_VALIDATE),
            'apply_value_1'    => 'bail|required|integer',
            'limited_amount'   => 'bail|nullable|integer|min:1',
            'is_never_limited' => 'bail|required|boolean',
            'start_datetime'   => 'bail|nullable|date_format:Y-m-d H:i:s',
            'end_datetime'     => 'bail|nullable|date_format:Y-m-d H:i:s',
            'is_never_expired' => 'bail|required|boolean',
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->promotionService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Update a program promotion
     * @group Promotion management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: 0
     * @bodyParam type integer required Example: 0
     * @bodyParam discount_amount integer required Example: 0
     * @bodyParam apply_type_1 integer required Example: 0
     * @bodyParam apply_value_1 integer Example: 0
     * @bodyParam apply_type_2 integer required Example: 0
     * @bodyParam apply_value_2 integer Example: 0
     * @bodyParam start_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam end_datetime datetime Example: 2021-10-20 00:00:00
     * @bodyParam is_never_expired boolean required Example: 0
     * @response {
        *   "status": true,
        *   "message": "Updated"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function updateProgram(Request $request, $id){
        $this->validate($request, [
            'name'             => 'bail|required',
            'type'             => 'bail|required|integer|in:'.implode(',', PromotionConst::TYPE_VALIDATE),
            'discount_amount'  => 'bail|required|integer|min:1',
            'apply_type_1'     => 'bail|required|integer|in:'.implode(',', PromotionConst::APPLY_TYPE_VALIDATE),
            'apply_value_1'    => 'bail|required|integer',
            'apply_type_2'     => 'bail|required|integer|in:'.implode(',', PromotionConst::APPLY_TYPE_2_VALIDATE),
            'apply_value_2'    => 'bail|required|integer',
            'start_datetime'   => 'bail|nullable|date_format:Y-m-d H:i:s',
            'end_datetime'     => 'bail|nullable|date_format:Y-m-d H:i:s',
            'is_never_expired' => 'bail|required|boolean',
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->promotionService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Delete promotions
     * @group Promotion management
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
            $result = $this->promotionService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
