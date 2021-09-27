<?php
namespace App\Repositories\Eloquent;

use App\Repositories\RouteInterface;
use App\Models\Route;

class RouteRepository implements RouteInterface {
    protected $model;
    function __construct(Route $route){
        $this->model = $route;
    }

    public function getAll($filter = []){
        $query = $this->model;
        if(!empty($filter)){
            if(isset($filter['type']) && $filter['type'] != '' ){
                $query = $query->where('type', $filter['type']);
            }
            if(isset($filter['type_id']) && $filter['type_id'] != '' ){
                $query = $query->where('type_id', $filter['type_id']);
            }
        }
        return $query->get();
    }

    public function create($data){
        return $this->model->create($data);
    }

    public function getByID($id){
        return $this->model->where('id', $id)->first();
    }

    public function getBySlug($slug){
        return $this->model->where('slug', $slug)->first();
    }

    public function getByTypeID($type, $type_id){
        return $this->model->where('type', $type)->where('type_id', $type_id)->first();
    }

    public function checkSlug($slug, $type = 0, $type_id = 0){
        $query = $this->model->where('slug', $slug);
        if ($type != 0) {
            $query = $query->where('type', $type);
        }
        if ($type_id != 0) {
            $query = $query->where('type_id', '!=',  $type_id);
        }
        return $query->first();
    }

    public function updateByID($id, $data){
        return $this->model->find($id)->update($data);
    }

    public function updateByTypeID($type, $type_id, $data){
        $route = $this->model->where('type', $type)->where('type_id', $type_id)->first();
        if (!$route) {
            return $this->create([
                'type' => $type,
                'type_id' => $type_id,
                'slug' => $data['slug'],
            ]);
        }
        return $route->update($data);
    }

    public function destroyByIDs($arrID){
        return $this->model->destroy($arrID);
    }

    public function destroyByTypeIDs($type, $arrID){
        return $this->model->where('type', $type)->whereIn('type_id', $arrID)->delete();
    }
}
