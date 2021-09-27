<?php
namespace App\Services;

use App\Repositories\AdvicePostInterface;
use App\Repositories\ProductCategoryInterface;

class AdvicePostService extends BaseService {
    protected $advicePost;
    protected $productCategory;
    function __construct(
        AdvicePostInterface $advicePost,
        ProductCategoryInterface $productCategory
    ){
        $this->advicePost = $advicePost;
        $this->productCategory = $productCategory;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->advicePost->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $category_ids = $data['category_ids'];
        unset($data['category_ids']);
        $age = $this->advicePost->create($data);
        $age->categories()->sync($category_ids);
        return $age;
    }

    public function getByID($id){
        return $this->advicePost->getByID($id);
    }

    public function updateByID($id, $data){
        $age = $this->advicePost->getByID($id);
        if(!$age){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $category_ids = $data['category_ids'];
        unset($data['category_ids']);
        $this->advicePost->updateByID($id, $data);
        $age->categories()->sync($category_ids);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->advicePost->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getAdvicePostByProductCategoryID($product_category_id){
        $category = $this->productCategory->getByID($product_category_id);
        if(!$category){
            return $this->_result(false, trans('messages.not_found'));
        }
        $posts = $category->advice_posts()->first();
        return $this->_result(true, '', $posts);
    }
}
