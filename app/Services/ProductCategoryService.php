<?php
namespace App\Services;

use App\Constants\RouteConst;
use App\Repositories\ProductCategoryInterface;
use App\Repositories\RouteInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductCategoryService extends BaseService {
    protected $productCategory;
    protected $route;
    function __construct(
        ProductCategoryInterface $productCategory,
        RouteInterface $route
    ){
        $this->productCategory = $productCategory;
        $this->route = $route;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $categories = $this->productCategory->getAllPaginate($perPage, $filter);
        $categories->load(['childrenRecursive', 'product_types', 'topics', 'ages']);
        return $categories;
    }

    public function create($data){
        $data['slug'] = Str::slug($data['name'], '-');
        $check = $this->productCategory->checkSlug($data['slug']);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $check = $this->route->checkSlug($data['slug']);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        if(isset($data['product_type_ids'])){
            $product_type_ids = $data['product_type_ids'];
            unset($data['product_type_ids']);
        }
        if(isset($data['topic_ids'])){
            $topic_ids = $data['topic_ids'];
            unset($data['topic_ids']);
        }
        if(isset($data['age_ids'])){
            $age_ids = $data['age_ids'];
            unset($data['age_ids']);
        }
        $category = $this->productCategory->create($data);
        if(isset($product_type_ids)){
            $category->product_types()->sync($product_type_ids);
        }
        if(isset($topic_ids)){
            $category->topics()->sync($topic_ids);
        }
        if(isset($age_ids)){
            $category->ages()->sync($age_ids);
        }
        $this->route->create([
            'slug' => $category->slug,
            'type' => RouteConst::TYPE_PRODUCT_CATEGORY,
            'type_id' => $category->id,
        ]);
        return $this->_result(true,  trans('messages.created_success'), $category);
    }

    public function getByID($id){
        $category = $this->productCategory->getByID($id);
        $category->load(['childrenRecursive', 'product_types', 'topics', 'ages']);
        return $category;
    }

    public function updateByID($id, $data){
        $category = $this->productCategory->getByID($id);
        if(!$category){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $data['slug'] = Str::slug($data['name'], '-');
        $check = $this->productCategory->checkSlug($data['slug'], $id);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $check = $this->route->checkSlug($data['slug'], RouteConst::TYPE_PRODUCT_CATEGORY, $id);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        if(isset($data['product_type_ids'])){
            $product_type_ids = $data['product_type_ids'];
            unset($data['product_type_ids']);
        }
        if(isset($data['topic_ids'])){
            $topic_ids = $data['topic_ids'];
            unset($data['topic_ids']);
        }
        if(isset($data['age_ids'])){
            $age_ids = $data['age_ids'];
            unset($data['age_ids']);
        }
        $this->productCategory->updateByID($id, $data);
        if(isset($product_type_ids)){
            $category->product_types()->sync($product_type_ids);
        }
        if(isset($topic_ids)){
            $category->topics()->sync($topic_ids);
        }
        if(isset($age_ids)){
            $category->ages()->sync($age_ids);
        }
        $this->route->updateByTypeID(RouteConst::TYPE_PRODUCT_CATEGORY, $id, [
            'slug' => $data['slug']
        ]);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        try {
            DB::beginTransaction();
            $result =$this->productCategory->destroyByIDs($arrID);
            if($result==0){
                return $this->_result(false, trans('messages.cannot_delete'));
            }
            $this->route->destroyByTypeIDs(RouteConst::TYPE_PRODUCT_CATEGORY, $arrID);
            DB::commit();
            return $this->_result(true, trans('messages.deleted_success'));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->_result(true, trans('messages.deleted_success'));
        }
    }

    public function getAll($filter = []){
        $categories = $this->productCategory->getAll($filter);
        $categories->load(['childrenRecursive']);
        return $categories;
    }

    public function getBySlug($slug){
        $category = $this->productCategory->getBySlug($slug);
        if(!$category){
            return $this->_result(false, trans('messages.not_found'));
        }
        return $this->_result(true, '', $category);
    }
}
