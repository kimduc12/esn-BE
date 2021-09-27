<?php
namespace App\Services;

use App\Repositories\CityInterface;

class CityService extends BaseService {
    protected $city;
    function __construct(
        CityInterface $city
    ){
        $this->city = $city;
    }

    public function getAll(){
        return $this->city->getAll();
    }
}
