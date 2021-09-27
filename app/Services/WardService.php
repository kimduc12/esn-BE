<?php
namespace App\Services;

use App\Repositories\WardInterface;

class WardService extends BaseService {
    protected $ward;
    function __construct(
        WardInterface $ward
    ){
        $this->ward = $ward;
    }

    public function getAll($filter = []){
        return $this->ward->getAll($filter);
    }
}
