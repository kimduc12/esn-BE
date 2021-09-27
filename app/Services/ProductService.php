<?php

namespace App\Services;

use App\Models\ProductOption;
use App\Constants\RouteConst;
use App\Repositories\ProductInterface;
use App\Repositories\RouteInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Attribute;

class ProductService extends BaseService
{
    protected $product;
    protected $route;
    function __construct(
        ProductInterface $product,
        RouteInterface $route
    ) {
        $this->product = $product;
        $this->route = $route;
    }

    public function getAllPaginate($perPage = 20, $filter = [])
    {
        return $this->product->getAllPaginate($perPage, $filter);
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            $data['slug'] = Str::slug($data['name'], '-');
            $check = $this->product->checkSlug($data['slug']);
            if ($check) {
                return $this->_result(false,  trans('messages.unique_slug'));
            }
            $check = $this->route->checkSlug($data['slug']);
            if ($check) {
                return $this->_result(false,  trans('messages.unique_slug'));
            }
            if (isset($data['category_ids'])) {
                $category_ids = $data['category_ids'];
                unset($data['category_ids']);
            }
            if (isset($data['age_ids'])) {
                $age_ids = $data['age_ids'];
                unset($data['age_ids']);
            }
            if (isset($data['topic_ids'])) {
                $topic_ids = $data['topic_ids'];
                unset($data['topic_ids']);
            }
            if (isset($data['product_type_ids'])) {
                $product_type_ids = $data['product_type_ids'];
                unset($data['product_type_ids']);
            }
            if (isset($data['pattern_ids'])) {
                $pattern_ids = $data['pattern_ids'];
                unset($data['pattern_ids']);
            }
            if (isset($data['material_ids'])) {
                $material_ids = $data['material_ids'];
                unset($data['material_ids']);
            }
            if (isset($data['file_ids'])) {
                $file_ids = $data['file_ids'];
                unset($data['file_ids']);
            }
            if (isset($data['options'])) {
                $options = $data['options'];
                unset($data['options']);
            }
            $product = $this->product->create($data);
            if (isset($category_ids)) {
                $product->categories()->sync($category_ids);
            }
            if (isset($age_ids)) {
                $product->ages()->sync($age_ids);
            }
            if (isset($topic_ids)) {
                $product->topics()->sync($topic_ids);
            }
            if (isset($product_type_ids)) {
                $product->product_types()->sync($product_type_ids);
            }
            if (isset($pattern_ids)) {
                $product->patterns()->sync($pattern_ids);
            }
            if (isset($material_ids)) {
                $product->materials()->sync($material_ids);
            }
            if (isset($file_ids)) {
                $product->files()->sync($file_ids);
            }
            if (isset($options)) {
                $product->product_options()->createMany($options);
            }
            $this->route->create([
                'slug' => $product->slug,
                'type' => RouteConst::TYPE_PRODUCT,
                'type_id' => $product->id,
            ]);
            DB::commit();
            return $this->_result(true,  trans('messages.created_success'), $product);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getByID($id)
    {
        $product = $this->product->getByID($id);
        if (!$product) {
            return false;
        }
        $product->load([
            'supplier', 'brand',
            'categories',
            'ages', 'topics', 'product_types', 'patterns', 'materials',
            'files',
            'product_options'
        ]);
        $attributes = Attribute::select('slug')->get();
        $product->product_options = $this->removeNullAttributeForProductOption($attributes, $product->product_options);
        return $product;
    }

    public function updateByID($id, $data)
    {
        $product = $this->product->getByID($id);
        if (!$product) {
            return $this->_result(false,  trans('messages.not_found'));
        }
        $data['slug'] = Str::slug($data['name'], '-');
        $check = $this->product->checkSlug($data['slug'], $id);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_slug'));
        }
        $check = $this->route->checkSlug($data['slug'], RouteConst::TYPE_PRODUCT, $id);
        if ($check) {
            return $this->_result(false,  trans('messages.unique_route_slug'));
        }
        if (isset($data['category_ids'])) {
            $category_ids = $data['category_ids'];
            unset($data['category_ids']);
        }
        if (isset($data['age_ids'])) {
            $age_ids = $data['age_ids'];
            unset($data['age_ids']);
        }
        if (isset($data['topic_ids'])) {
            $topic_ids = $data['topic_ids'];
            unset($data['topic_ids']);
        }
        if (isset($data['product_type_ids'])) {
            $product_type_ids = $data['product_type_ids'];
            unset($data['product_type_ids']);
        }
        if (isset($data['pattern_ids'])) {
            $pattern_ids = $data['pattern_ids'];
            unset($data['pattern_ids']);
        }
        if (isset($data['material_ids'])) {
            $material_ids = $data['material_ids'];
            unset($data['material_ids']);
        }
        if (isset($data['file_ids'])) {
            $file_ids = $data['file_ids'];
            unset($data['file_ids']);
        }
        $this->product->updateByID($id, $data);
        if (isset($category_ids)) {
            $product->categories()->sync($category_ids);
        }
        if (isset($age_ids)) {
            $product->ages()->sync($age_ids);
        }
        if (isset($topic_ids)) {
            $product->topics()->sync($topic_ids);
        }
        if (isset($product_type_ids)) {
            $product->product_types()->sync($product_type_ids);
        }
        if (isset($pattern_ids)) {
            $product->patterns()->sync($pattern_ids);
        }
        if (isset($material_ids)) {
            $product->materials()->sync($material_ids);
        }
        if (isset($file_ids)) {
            $product->files()->sync($file_ids);
        }
        $this->route->updateByTypeID(RouteConst::TYPE_PRODUCT, $id, [
            'slug' => $data['slug']
        ]);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID)
    {
        try {
            DB::beginTransaction();
            $result = $this->product->destroyByIDs($arrID);
            if ($result == 0) {
                return $this->_result(false, trans('messages.cannot_delete'));
            }
            $this->route->destroyByTypeIDs(RouteConst::TYPE_PRODUCT, $arrID);
            DB::commit();
            return $this->_result(true, trans('messages.deleted_success'));
        } catch (\Exception $e) {
            DB::rollback();
            \Log::info("destroyByIDs", [$e]);
            return $this->_result(true, trans('messages.deleted_success'));
        }
    }

    public function getActiveListPaginate($perPage = 20, $filter = [])
    {
        $products = $this->product->getActiveListPaginate($perPage, $filter);
        $products->load([
            'product_options'
        ]);
        return $products;
    }

    public function getAllActive($filter = [])
    {
        return $this->product->getAllActive($filter);
    }

    public function getBySlug($slug)
    {
        $product = $this->product->getBySlug($slug);
        if (!$product) {
            return $this->_result(false, trans('messages.not_found'));
        }
        $product->load([
            'product_options', 'supplier', 'brand', 'country', 'files'
        ]);
        $attributes = Attribute::select('slug')->get();
        $product->product_options = $this->removeNullAttributeForProductOption($attributes, $product->product_options);
        return $this->_result(true, '', $product);
    }

    public function createOption($id, $data)
    {
        try {
            DB::beginTransaction();
            $product = $this->product->getByID($id);
            if (!$product) {
                return $this->_result(false, trans('messages.not_found'));
            }
            $option = $product->product_options()->create($data);
            DB::commit();
            return $this->_result(true, '', $option);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateOptionByID($id, $option_id, $data)
    {
        $product = $this->product->getByID($id);
        if (!$product) {
            return $this->_result(false,  trans('messages.not_found'));
        }
        $option = $product->product_options()->find($option_id);
        if (!$option) {
            return $this->_result(false,  trans('messages.not_found'));
        }
        $option->update($data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyOptionByIDs($id, $option_ids)
    {
        try {
            DB::beginTransaction();
            $product = $this->product->getByID($id);
            if (!$product) {
                return $this->_result(false,  trans('messages.not_found'));
            }

            $result = $product->product_options()->whereIn('id', $option_ids)->delete();
            if ($result == 0) {
                DB::rollback();
                return $this->_result(false, trans('messages.cannot_delete'));
            }
            DB::commit();
            return $this->_result(true, trans('messages.deleted_success'));
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateFiles($id, $data)
    {
        $product = $this->product->getByID($id);
        if (!$product) {
            return $this->_result(false,  trans('messages.not_found'));
        }

        $file_ids = $data['file_ids'];
        $product->files()->sync($file_ids);

        return $this->_result(true,  trans('messages.update_success'));
    }
}
