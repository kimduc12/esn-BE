<?php
namespace App\Services;

use App\Constants\RouteConst;
use App\Repositories\TopicInterface;
use App\Repositories\RouteInterface;
use Illuminate\Support\Facades\DB;

class TopicService extends BaseService {
    protected $topic;
    protected $route;
    function __construct(
        TopicInterface $topic,
        RouteInterface $route
    ){
        $this->topic = $topic;
        $this->route = $route;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->topic->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $check = $this->route->checkSlug($data['slug']);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        if(isset($data['category_ids'])) {
            $category_ids = $data['category_ids'];
            unset($data['category_ids']);
        }
        if(isset($data['home_product_ids'])) {
            $home_product_ids = $data['home_product_ids'];
            unset($data['home_product_ids']);
        }
        if($data['is_active'] == true) {
            $this->topic->update(['is_active' => false]);
        }
        $topic = $this->topic->create($data);
        if(isset($category_ids)) {
            $topic->categories()->sync($category_ids);
        }
        if(isset($home_product_ids)) {
            $topic->home_products()->sync($home_product_ids);
        }
        $this->route->create([
            'slug' => $topic->slug,
            'type' => RouteConst::TYPE_TOPIC,
            'type_id' => $topic->id,
        ]);
        return $this->_result(true,  trans('messages.created_success'), $topic);
    }

    public function getByID($id){
        $topic = $this->topic->getByID($id);
        $topic->load(['categories', 'home_products']);
        return $topic;
    }

    public function updateByID($id, $data){
        $topic = $this->topic->getByID($id);
        if(!$topic){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $check = $this->route->checkSlug($data['slug'], RouteConst::TYPE_TOPIC, $id);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        if(isset($data['category_ids'])) {
            $category_ids = $data['category_ids'];
            unset($data['category_ids']);
        }
        if(isset($data['home_product_ids'])) {
            $home_product_ids = $data['home_product_ids'];
            unset($data['home_product_ids']);
        }
        if($data['is_active'] == true) {
            $this->topic->update(['is_active' => false]);
        }
        $this->topic->updateByID($id, $data);
        if(isset($category_ids)) {
            $topic->categories()->sync($category_ids);
        }
        if(isset($home_product_ids)) {
            $topic->home_products()->sync($home_product_ids);
        }
        $this->route->updateByTypeID(RouteConst::TYPE_TOPIC, $id, [
            'slug' => $data['slug']
        ]);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        try {
            DB::beginTransaction();
            $result =$this->topic->destroyByIDs($arrID);
            if($result==0){
                return $this->_result(false, trans('messages.cannot_delete'));
            }
            $this->route->destroyByTypeIDs(RouteConst::TYPE_TOPIC, $arrID);
            DB::commit();
            return $this->_result(true, trans('messages.deleted_success'));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->_result(true, trans('messages.deleted_success'));
        }
    }

    public function getListPaginate($perPage = 20, $filter = []){
        return $this->topic->getListPaginate($perPage, $filter);
    }

    public function getAll($filter = []){
        return $this->topic->getAll($filter);
    }

    public function getOneActive(){
        $topic = $this->topic->getOneActive();
        $topic->load(['home_products']);
        return $topic;
    }
}
