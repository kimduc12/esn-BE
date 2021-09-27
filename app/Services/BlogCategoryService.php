<?php
namespace App\Services;

use App\Constants\RouteConst;
use App\Repositories\BlogCategoryInterface;
use App\Repositories\RouteInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BlogCategoryService extends BaseService {
    protected $blogCategory;
    protected $route;
    function __construct(
        BlogCategoryInterface $blogCategory,
        RouteInterface $route
    ){
        $this->blogCategory = $blogCategory;
        $this->route = $route;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->blogCategory->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $data['slug'] = Str::slug($data['name'], '-');
        $check = $this->blogCategory->checkSlug($data['slug']);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $check = $this->route->checkSlug($data['slug']);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $blogCategory = $this->blogCategory->create($data);
        $this->route->create([
            'slug' => $blogCategory->slug,
            'type' => RouteConst::TYPE_BLOG_CATEGORY,
            'type_id' => $blogCategory->id,
        ]);
        return $this->_result(true,  trans('messages.created_success'), $blogCategory);
    }

    public function getByID($id){
        return $this->blogCategory->getByID($id);
    }

    public function updateByID($id, $data){
        $blog = $this->blogCategory->getByID($id);
        if(!$blog){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $data['slug'] = Str::slug($data['name'], '-');
        $check = $this->blogCategory->checkSlug($data['slug'], $id);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $check = $this->route->checkSlug($data['slug'], RouteConst::TYPE_BLOG_CATEGORY, $id);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $this->blogCategory->updateByID($id, $data);
        $this->route->updateByTypeID(RouteConst::TYPE_BLOG_CATEGORY, $id, [
            'slug' => $data['slug']
        ]);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        try {
            DB::beginTransaction();
            $result =$this->blogCategory->destroyByIDs($arrID);
            if($result==0){
                DB::rollback();
                return $this->_result(false, trans('messages.cannot_delete'));
            }
            $this->route->destroyByTypeIDs(RouteConst::TYPE_BLOG_CATEGORY, $arrID);
            DB::commit();
            return $this->_result(true, trans('messages.deleted_success'));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->_result(true, trans('messages.deleted_success'));
        }
    }

    public function getAll($filter = []){
        return $this->blogCategory->getAll($filter);
    }

    public function getBySlug($slug){
        $blog = $this->blogCategory->getBySlug($slug);
        if(!$blog){
            return $this->_result(false, trans('messages.not_found'));
        }
        return $this->_result(true, '', $blog);
    }
}
