<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Services\WardService;

class WardController extends RestfulController
{
    protected $wardService;
    public function __construct(WardService $wardService){
        parent::__construct();
        $this->wardService = $wardService;
    }

    /**
     * Get wards
     * @group Ward management
     * @queryParam district_id integer Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "name": "Phường 1",
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
            $district_id = $request->input('district_id', 0);
            $filter = [
                'district_id'       => $district_id,
            ];
            $wards = $this->wardService->getAll($filter);
            return $this->_response($wards);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
