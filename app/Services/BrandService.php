<?php
namespace App\Services;

use App\Repositories\BrandInterface;

class BrandService extends BaseService {
    protected $brand;
    function __construct(
        BrandInterface $brand
    ){
        $this->brand = $brand;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->brand->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $brand = $this->brand->create($data);
        return $brand;
    }

    public function getByID($id){
        return $this->brand->getByID($id);
    }

    public function updateByID($id, $data){
        $brand = $this->brand->getByID($id);
        if(!$brand){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $this->brand->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->brand->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getAll($filter = []){
        return $this->brand->getAll($filter);
    }
}
