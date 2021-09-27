<?php
namespace App\Services;

use App\Repositories\MaterialInterface;

class MaterialService extends BaseService {
    protected $material;
    function __construct(
        MaterialInterface $material
    ){
        $this->material = $material;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->material->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $brand = $this->material->create($data);
        return $brand;
    }

    public function getByID($id){
        return $this->material->getByID($id);
    }

    public function updateByID($id, $data){
        $brand = $this->material->getByID($id);
        if(!$brand){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $this->material->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->material->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getAll($filter = []){
        return $this->material->getAll($filter);
    }
}
