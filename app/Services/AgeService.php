<?php
namespace App\Services;

use App\Repositories\AgeInterface;

class AgeService extends BaseService {
    protected $age;
    function __construct(
        AgeInterface $age
    ){
        $this->age = $age;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->age->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $category_ids = $data['category_ids'];
        unset($data['category_ids']);
        $age = $this->age->create($data);
        $age->categories()->sync($category_ids);
        return $age;
    }

    public function getByID($id){
        return $this->age->getByID($id);
    }

    public function updateByID($id, $data){
        $age = $this->age->getByID($id);
        if(!$age){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $category_ids = $data['category_ids'];
        unset($data['category_ids']);
        $this->age->updateByID($id, $data);
        $age->categories()->sync($category_ids);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->age->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getListPaginate($perPage = 20, $filter = []){
        return $this->age->getListPaginate($perPage, $filter);
    }

    public function getAll($filter = []){
        return $this->age->getAll($filter);
    }
}
