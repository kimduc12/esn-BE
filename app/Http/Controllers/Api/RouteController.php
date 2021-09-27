<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Services\RouteService;

class RouteController extends RestfulController
{
    protected $routeService;
    public function __construct(RouteService $routeService){
        parent::__construct();
        $this->routeService = $routeService;
    }

    /**
     * Get all routes
     * @group Route management
     * @queryParam type integer Field to filter. Defaults to 0
     * @queryParam type_id integer Field to filter. Defaults to 0
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "slug": "1 tuoi",
                    *   "type": 1,
                    *   "type_id": 1,
                    *   "type_name": "product",
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
            $type_id = $request->input('type_id', 0);
            $filter = [
                'type'    => $type,
                'type_id' => $type_id,
            ];
            $routes = $this->routeService->getAll($filter);
            return $this->_response($routes);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get a route by slug
     * @group Route management
     * @urlParam slug string required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "slug": "1 tuoi",
                *   "type": 1,
                *   "type_id": 1,
                *   "type_name": "product",
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00"
            *   }
        * }
     * @response status=200 scenario="Not found" {
        *  "status": false,
        *  "message": "Not found"
        * }
     */
    public function getBySlug($slug){
        try{
            $result = $this->routeService->getBySlug($slug);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
