<?php
namespace App\Repositories\Eloquent;

use App\Constants\ProductConst;
use App\Repositories\ProductInterface;
use App\Models\Product;

class ProductRepository implements ProductInterface {
    protected $model;
    function __construct(Product $product){
        $this->model = $product;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model->with(['supplier','categories']);
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['status']) && $filter['status'] != '' ){
                $query = $query->where('status', $filter['status']);
            }
            if(isset($filter['brand_id']) && $filter['brand_id'] != '' ){
                $query = $query->where('brand_id', $filter['brand_id']);
            }
            if(isset($filter['supplier_id']) && $filter['supplier_id'] != '' ){
                $query = $query->where('supplier_id', $filter['supplier_id']);
            }
            if(isset($filter['country_id']) && $filter['country_id'] != '' ){
                $query = $query->where('country_id', $filter['country_id']);
            }
            if(isset($filter['category_ids']) && $filter['category_ids'] != ''){
                $category_ids = $filter['category_ids'];
                $query = $query->whereHas('categories', function($query) use ($category_ids) {
                    $query->whereIn('id', explode(",", $category_ids));
                });
            }
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create($data){
        return $this->model->create($data);
    }

    public function getByID($id){
        return $this->model->where('id', $id)->first();
    }

    public function updateByID($id,$data){
        return $this->model->find($id)->update($data);
    }

    public function destroyByIDs($arrID){
        return $this->model->destroy($arrID);
    }

    public function getActiveListPaginate($perPage = 20, $filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['supplier_id']) && $filter['supplier_id'] != 0){
                $supplier_id = $filter['supplier_id'];
                $query = $query->where('supplier_id', $supplier_id);
            }
            if(isset($filter['brand_id']) && $filter['brand_id'] != 0){
                $brand_id = $filter['brand_id'];
                $query = $query->where('brand_id', $brand_id);
            }
            if(isset($filter['country_id']) && $filter['country_id'] != '' ){
                $query = $query->where('country_id', $filter['country_id']);
            }
            if(isset($filter['category_id']) && $filter['category_id'] != 0){
                $category_id = $filter['category_id'];
                $query = $query->whereHas('categories', function($query) use ($category_id) {
                    $query->where('id', $category_id);
                });
            }
            if(isset($filter['from_price']) && $filter['from_price'] != 0){
                $from_price = $filter['from_price'];
                $query = $query->where('price', '>=', $from_price);
            }
            if(isset($filter['to_price']) && $filter['to_price'] != 0){
                $to_price = $filter['to_price'];
                $query = $query->where('price', '<=', $to_price);
            }
            if(isset($filter['age_id']) && $filter['age_id'] != 0){
                $age_id = $filter['age_id'];
                $query = $query->whereHas('ages', function($query) use ($age_id) {
                    $query->where('id', $age_id);
                });
            }
            if(isset($filter['topic_id']) && $filter['topic_id'] != 0){
                $topic_id = $filter['topic_id'];
                $query = $query->whereHas('topics', function($query) use ($topic_id) {
                    $query->where('id', $topic_id);
                });
            }
            if(isset($filter['product_type_id']) && $filter['product_type_id'] != 0){
                $product_type_id = $filter['product_type_id'];
                $query = $query->whereHas('product_types', function($query) use ($product_type_id) {
                    $query->where('id', $product_type_id);
                });
            }
            if(isset($filter['pattern_id']) && $filter['pattern_id'] != 0){
                $pattern_id = $filter['pattern_id'];
                $query = $query->whereHas('patterns', function($query) use ($pattern_id) {
                    $query->where('id', $pattern_id);
                });
            }
            if(isset($filter['material_id']) && $filter['material_id'] != 0){
                $material_id = $filter['material_id'];
                $query = $query->whereHas('materials', function($query) use ($material_id) {
                    $query->where('id', $material_id);
                });
            }
            if(isset($filter['star_rating']) && $filter['star_rating'] != 0){
                $star_rating = $filter['star_rating'];
                $query = $query->where('star_rating', $star_rating);
            }

            if(isset($filter['star_rating_gte']) && $filter['star_rating_gte'] != 0){
                $star_rating_gte = $filter['star_rating_gte'];
                $query = $query->where('star_rating', '>=', $star_rating_gte);
            }

            if(isset($filter['star_rating_lte']) && $filter['star_rating_lte'] != 0){
                $star_rating_lte = $filter['star_rating_lte'];
                $query = $query->where('star_rating', '<=', $star_rating_lte);
            }

            if(isset($filter['from_date']) && $filter['from_date'] != '' ){
                $query = $query->where('published_at', '>=', $filter['from_date']);
            }
            if(isset($filter['to_date']) && $filter['to_date'] != '' ){
                $query = $query->where('published_at', '<=', $filter['to_date']);
            }

            foreach ($filter as $key => $value) {
                if ($value && $value != '') {
                    if($key == 'attribute_color') {
                        $query = $query->whereHas('product_options', function ($q) use ($value){
                            $q = $q->hasAttribute('color', $value);
                        });
                    }
                    if($key == 'attribute_size') {
                        $query = $query->whereHas('product_options', function ($q) use ($value){
                            $q = $q->hasAttribute('size', $value);
                        });
                    }
                }
            }
        }
        $query = $query->actived()
                        ->published()
                        ->showed();
        if(!empty($filter) && isset($filter['sort_by'])){
            $sort_by = $filter['sort_by'];
            switch ($sort_by) {
                case "popular":
                    $query = $query->where('is_popular', 1);
                    $query = $query->orderBy('created_at', 'desc');
                    break;
                case "newest":
                    $query = $query->orderBy('created_at', 'desc');
                    break;
                case "selling":
                    $query = $query->orderBy('total_orders', 'desc');
                    break;
                case "price_asc":
                    $query = $query->orderBy('price', 'asc');
                    break;
                case "price_desc":
                    $query = $query->orderBy('price', 'desc');
                    break;
            }
        } else {
            $query = $query->orderBy('created_at', 'desc');
        }
        return $query->paginate($perPage);
    }

    public function getAllActive($filter = []){
        $query = $this->model->with(['categories']);
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
            if(isset($filter['supplier_id']) && $filter['supplier_id'] != 0){
                $supplier_id = $filter['supplier_id'];
                $query = $query->where('supplier_id', $supplier_id);
            }
            if(isset($filter['brand_id']) && $filter['brand_id'] != 0){
                $brand_id = $filter['brand_id'];
                $query = $query->where('brand_id', $brand_id);
            }
            if(isset($filter['country_id']) && $filter['country_id'] != '' ){
                $query = $query->where('country_id', $filter['country_id']);
            }
            if(isset($filter['category_id']) && $filter['category_id'] != 0){
                $category_id = $filter['category_id'];
                $query = $query->whereHas('categories', function($query) use ($category_id) {
                    $query->where('id', $category_id);
                });
            }
            if(isset($filter['product_type_id']) && $filter['product_type_id'] != 0){
                $product_type_id = $filter['product_type_id'];
                $query = $query->whereHas('product_types', function($query) use ($product_type_id) {
                    $query->where('id', $product_type_id);
                });
            }
            if(isset($filter['from_date']) && $filter['from_date'] != '' ){
                $query = $query->where('published_at', '>=', $filter['from_date']);
            }
            if(isset($filter['to_date']) && $filter['to_date'] != '' ){
                $query = $query->where('published_at', '<=', $filter['to_date']);
            }
        }
        $query = $query->actived()
                        ->published()
                        ->showed();
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getBySlug($slug){
        $query = $this->model;
        $query = $query->where('slug', $slug);
        $query = $query->actived()
                        ->published()
                        ->showed();
        return $query->first();
    }

    public function checkSlug($slug, $id = 0){
        $query = $this->model->where('slug', $slug);
        if ($id != 0) {
            $query = $query->where('id', '!=',  $id);
        }
        return $query->first();
    }
}
