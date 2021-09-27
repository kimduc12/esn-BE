<?php
namespace App\Services;

use App\Repositories\PageInterface;
use Illuminate\Support\Str;

class PageService extends BaseService {
    protected $page;
    function __construct(
        PageInterface $page
    ){
        $this->page = $page;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->page->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $data['slug'] = Str::slug($data['name'], '-');
        $check = $this->page->checkSlug($data['slug']);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $page = $this->page->create($data);
        return $page;
    }

    public function getByID($id){
        return $this->page->getByID($id);
    }

    public function updateByID($id, $data){
        $page = $this->page->getByID($id);
        if(!$page){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $data['slug'] = Str::slug($data['name'], '-');
        $check = $this->page->checkSlug($data['slug'], $id);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $this->page->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->page->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getListPaginate($perPage = 20, $filter = []){
        return $this->page->getListPaginate($perPage, $filter);
    }

    public function getAll($filter = []){
        return $this->page->getAll($filter);
    }

    public function getBySlug($slug){
        $page = $this->page->getBySlug($slug);
        if(!$page){
            return $this->_result(false, trans('messages.not_found'));
        }
        return $this->_result(true, '', $page);
    }
}
