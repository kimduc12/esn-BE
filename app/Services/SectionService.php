<?php
namespace App\Services;

use App\Repositories\SectionInterface;

class SectionService extends BaseService {
    protected $section;
    function __construct(
        SectionInterface $section
    ){
        $this->section = $section;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->section->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        if(isset($data['product_ids'])) {
            $product_ids = $data['product_ids'];
            unset($data['product_ids']);
        }

        if(isset($data['blog_ids'])) {
            $blog_ids = $data['blog_ids'];
            unset($data['blog_ids']);
        }

        $section = $this->section->create($data);
        if(isset($product_ids)) {
            $section->products()->sync($product_ids);
        }
        if(isset($blog_ids)) {
            $section->blogs()->sync($blog_ids);
        }
        return $section;
    }

    public function getByID($id){
        $section = $this->section->getByID($id);
        if($section->type == 1) {
            $section->items = $section->products()->get();
        }
        if($section->type == 2) {
            $section->items = $section->blogs()->get();
        }
        return $section;
    }

    public function updateByID($id, $data){
        $section = $this->section->getByID($id);
        if(!$section){
            return $this->_result(false,  trans('messages.not_found'));
        }
        if(isset($data['product_ids'])) {
            $product_ids = $data['product_ids'];
            unset($data['product_ids']);
        }

        if(isset($data['blog_ids'])) {
            $blog_ids = $data['blog_ids'];
            unset($data['blog_ids']);
        }
        $this->section->updateByID($id, $data);
        if(isset($product_ids)) {
            $section->products()->sync($product_ids);
        }
        if(isset($blog_ids)) {
            $section->blogs()->sync($blog_ids);
        }
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->section->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getOneByPosition($position){
        $section = $this->section->getOneByPosition($position);
        if (!$section) {
            return $this->_result(false, trans('messages.not_found'));
        }
        if($section->type == 1) {
            $section->items = $section->products()->get();
        }
        if($section->type == 2) {
            $section->items = $section->blogs()->get();
        }
        return $this->_result(true, "", $section);
    }
}
