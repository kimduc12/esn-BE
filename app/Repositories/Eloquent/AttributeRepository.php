<?php
namespace App\Repositories\Eloquent;

use App\Constants\AttributeConst;
use App\Repositories\AttributeInterface;
use App\Models\Attribute;
use Illuminate\Database\Eloquent\Builder;

class AttributeRepository implements AttributeInterface {
    protected $model;
    function __construct(Attribute $attribute){
        $this->model = $attribute;
    }

    public function getAll(){
        $query = $this->model;
        return $query->get();
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

    public function getByEntity($entity){
        $query = $this->model;
        $query = $query->whereHas('entities', function(Builder $q) use ($entity) {
            $q->where('entity_type', 'App\Models\\'.$entity);
        });
        return $query->get();
    }

    public function getByEntityAndEntityID($entity, $entity_id){
        $query = $this->model;
        $query = $query->whereHas('entities', function(Builder $q) use ($entity) {
            $q->where('entity_type', 'App\Models\\'.$entity);
        });
        $query = $query->whereHas('varcharValues', function(Builder $q) use ($entity_id) {
            $q->where('entity_id', $entity_id);
        });
        return $query->get();
    }
}
