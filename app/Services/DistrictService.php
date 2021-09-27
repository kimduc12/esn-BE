<?php
namespace App\Services;

use App\Repositories\DistrictInterface;

class DistrictService extends BaseService {
    protected $district;
    function __construct(
        DistrictInterface $district
    ){
        $this->district = $district;
    }

    public function getAll($filter = []){
        return $this->district->getAll($filter);
    }
}
