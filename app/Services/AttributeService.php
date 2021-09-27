<?php
namespace App\Services;

use App\Repositories\AttributeInterface;
use Illuminate\Support\Facades\DB;

class AttributeService extends BaseService {
    protected $attribute;
    function __construct(
        AttributeInterface $attribute
    ){
        $this->attribute = $attribute;
    }

    public function getAll(){
        return $this->attribute->getAll();
    }

    public function create($data){
        $entities = $data['entities'];
        foreach ($entities as &$entity)
        {
            $entity = implode("\\", ["App", "Models", $entity] );
        }
        $data['entities'] = $entities;

        $attribute = $this->attribute->create($data);
        return $attribute;
    }

    public function getByID($id){
        $attribute = $this->attribute->getByID($id);
        // dd($attribute->values(\Rinvex\Attributes\Models\Type\Varchar::class)->get());
        return $attribute;
    }

    public function updateByID($id, $data){
        $attribute = $this->attribute->getByID($id);
        if(!$attribute){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $entities = $data['entities'];
        foreach ($entities as &$entity)
        {
            $entity = implode("\\", ["App", "Models", $entity] );
        }
        $data['entities'] = $entities;
        $this->attribute->updateByID($id, $data);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $result =$this->attribute->destroyByIDs($arrID);
        if($result==0){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getByEntity($entity){
        $attributes = $this->attribute->getByEntity($entity);
        foreach($attributes as &$attribute) {
            $attribute->data = $attribute
                                ->values($this->convertValueToClass($attribute->type))
                                ->select(DB::raw('id, content'))
                                ->groupBy('content')
                                ->get();
        }
        // dd($attributes);
        // dd($attributes->values(\Rinvex\Attributes\Models\Type\Varchar::class)->get());
        return $attributes;
    }

    protected function convertValueToClass($value) {
        switch($value){
            case 'varchar':
                return \Rinvex\Attributes\Models\Type\Varchar::class;
                break;
            default:
                return \Rinvex\Attributes\Models\Type\Varchar::class;
        }
    }

    public function getByEntityAndEntityID($entity, $entity_id){
        $attributes = $this->attribute->getByEntityAndEntityID($entity, $entity_id);
        foreach($attributes as &$attribute) {
            $attribute->data = $attribute
                                ->values($this->convertValueToClass($attribute->type))
                                ->whereEntityId($entity_id)
                                ->select(DB::raw('id, content'))
                                ->groupBy('content')
                                ->get();
        }
        return $attributes;
    }
}
