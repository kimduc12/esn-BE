<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Services\DistrictService;

class DistrictController extends RestfulController
{
    protected $districtService;
    public function __construct(DistrictService $districtService){
        parent::__construct();
        $this->districtService = $districtService;
    }

    /**
     * Get districts
     * @group District management
     * @queryParam city_id integer Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Quận 1",
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
            $city_id = $request->input('city_id', 0);
            $filter = [
                'city_id'       => $city_id,
            ];
            $districts = $this->districtService->getAll($filter);
            return $this->_response($districts);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
