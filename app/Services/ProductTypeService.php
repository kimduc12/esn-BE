<?php
namespace App\Services;

use App\Repositories\ProductTypeInterface;

class ProductTypeService extends BaseService {
    protected $product_type;
    function __construct(
        ProductTypeInterface $product_type
    ){
        $this->product_type = $product_type;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->product_type->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $category_ids = $data['category_ids'];
        unset($data['category_ids']);
        $productType = $this->product_type->create($data);
        $productType->categories()->sync($category_ids);
        return $productType;
    }

    public function getByID($id){
        return $this->product_type->getByID($id);
    }

    public function updateByID($id, $data){
        $productType = $this->product_type->getByID($id);
        if(!$productType){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $category_ids = $data['category_ids'];
        unset($data['category_ids']);
        $this->product_type->updateByID($id, $data);
        $productType->categories()->sync($category_ids);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->product_type->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getListPaginate($perPage = 20, $filter = []){
        return $this->product_type->getListPaginate($perPage, $filter);
    }

    public function getAll($filter = []){
        $types = $this->product_type->getAll($filter);
        $types->load(['categories']);
        return $types;
    }
}
