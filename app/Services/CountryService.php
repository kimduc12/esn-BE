<?php
namespace App\Services;

use App\Repositories\CountryInterface;

class CountryService extends BaseService {
    protected $country;
    function __construct(
        CountryInterface $country
    ){
        $this->country = $country;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->country->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $age = $this->country->create($data);
        return $age;
    }

    public function getByID($id){
        return $this->country->getByID($id);
    }

    public function updateByID($id, $data){
        $age = $this->country->getByID($id);
        if(!$age){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $this->country->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->country->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getAllActive($filter = []){
        return $this->country->getAllActive($filter);
    }
}
