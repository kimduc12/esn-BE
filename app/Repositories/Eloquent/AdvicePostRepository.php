<?php
namespace App\Repositories\Eloquent;

use App\Repositories\AdvicePostInterface;
use App\Models\AdvicePost;

class AdvicePostRepository implements AdvicePostInterface {
    protected $model;
    function __construct(AdvicePost $post){
        $this->model = $post;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['product_category_ids']) && $filter['product_category_ids'] != ''){
                $product_category_ids = $filter['product_category_ids'];
                $query = $query->whereHas('categories', function($query) use ($product_category_ids) {
                    $query->whereIn('product_category_id', explode(",", $product_category_ids));
                });
            }
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create($data){
        return $this->model->create($data);
    }

    public function getByID($id){
        return $this->model->with(['categories'])->where('id', $id)->first();
    }

    public function updateByID($id,$data){
        return $this->model->find($id)->update($data);
    }

    public function destroyByIDs($arrID){
        return $this->model->destroy($arrID);
    }
}
