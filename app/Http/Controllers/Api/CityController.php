<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Services\CityService;

class CityController extends RestfulController
{
    protected $cityService;
    public function __construct(CityService $cityService){
        parent::__construct();
        $this->cityService = $cityService;
    }

    /**
     * Get cities
     * @group City management
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Ho Chi Minh",
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
            $cities = $this->cityService->getAll();
            return $this->_response($cities);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
