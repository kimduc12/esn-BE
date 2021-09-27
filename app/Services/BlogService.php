<?php
namespace App\Services;

use App\Constants\RouteConst;
use App\Repositories\BlogInterface;
use App\Repositories\RouteInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BlogService extends BaseService {
    protected $blog;
    protected $route;
    function __construct(
        BlogInterface $blog,
        RouteInterface $route
    ){
        $this->blog = $blog;
        $this->route = $route;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->blog->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $data['slug'] = Str::slug($data['name'], '-');
        $check = $this->blog->checkSlug($data['slug']);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $check = $this->route->checkSlug($data['slug']);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        if ($data['is_top'] == true) {
            $this->blog->updateAll(['is_top' => 0]);
        }
        $category_ids = $data['category_ids'];
        unset($data['category_ids']);
        $blog = $this->blog->create($data);
        $blog->categories()->sync($category_ids);
        $this->route->create([
            'slug' => $blog->slug,
            'type' => RouteConst::TYPE_BLOG,
            'type_id' => $blog->id,
        ]);
        return $this->_result(true,  trans('messages.created_success'), $blog);
    }

    public function getByID($id){
        $blog = $this->blog->getByID($id);
        $blog->load(['categories']);
        return $blog;
    }

    public function updateByID($id,$data){
        $blog = $this->blog->getByID($id);
        if(!$blog){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $data['slug'] = Str::slug($data['name'], '-');
        $check = $this->blog->checkSlug($data['slug'], $id);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $check = $this->route->checkSlug($data['slug'], RouteConst::TYPE_BLOG, $id);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        if ($data['is_top'] == true) {
            $this->blog->updateAll(['is_top' => 0]);
        }
        $category_ids = $data['category_ids'];
        unset($data['category_ids']);
        $this->blog->updateByID($id, $data);
        $blog->categories()->sync($category_ids);
        $this->route->updateByTypeID(RouteConst::TYPE_BLOG, $id, [
            'slug' => $data['slug']
        ]);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        try {
            DB::beginTransaction();
            $result =$this->blog->destroyByIDs($arrID);
            if($result==0){
                return $this->_result(false, trans('messages.cannot_delete'));
            }
            $this->route->destroyByTypeIDs(RouteConst::TYPE_BLOG, $arrID);
            DB::commit();
            return $this->_result(true, trans('messages.deleted_success'));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->_result(true, trans('messages.deleted_success'));
        }
    }

    public function getListPaginate($perPage = 20, $filter = []){
        $blogs = $this->blog->getListPaginate($perPage, $filter);
        $blogs->load(['categories']);
        return $blogs;
    }


    public function getAll($filter = []){
        $blog = $this->blog->getAll($filter);
        $blog->load(['categories']);
        return $blog;
    }

    public function getBySlug($slug){
        $blog = $this->blog->getBySlug($slug);
        if(!$blog){
            return $this->_result(false, trans('messages.not_found'));
        }
        return $this->_result(true, '', $blog);
    }
}
