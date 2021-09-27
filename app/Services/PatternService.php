<?php
namespace App\Services;

use App\Repositories\PatternInterface;

class PatternService extends BaseService {
    protected $pattern;
    function __construct(
        PatternInterface $pattern
    ){
        $this->pattern = $pattern;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->pattern->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $brand = $this->pattern->create($data);
        return $brand;
    }

    public function getByID($id){
        return $this->pattern->getByID($id);
    }

    public function updateByID($id, $data){
        $brand = $this->pattern->getByID($id);
        if(!$brand){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $this->pattern->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->pattern->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getAll($filter = []){
        return $this->pattern->getAll($filter);
    }
}
