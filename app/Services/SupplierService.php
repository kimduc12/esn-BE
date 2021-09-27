<?php
namespace App\Services;

use App\Repositories\SupplierInterface;

class SupplierService extends BaseService {
    protected $supplier;
    function __construct(
        SupplierInterface $supplier
    ){
        $this->supplier = $supplier;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->supplier->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $age = $this->supplier->create($data);
        return $age;
    }

    public function getByID($id){
        return $this->supplier->getByID($id);
    }

    public function updateByID($id, $data){
        $age = $this->supplier->getByID($id);
        if(!$age){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $this->supplier->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->supplier->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getListPaginate($perPage = 20, $filter = []){
        return $this->supplier->getListPaginate($perPage, $filter);
    }

    public function getAll($filter = []){
        return $this->supplier->getAll($filter);
    }
}
