<?php
namespace App\Repositories\Eloquent;

use App\Repositories\RoleInterface;
use App\Models\Role;

class RoleRepository implements RoleInterface {
    protected $model;
    function __construct(Role $role){
        $this->model = $role;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        $query = $this->model->with(['permissions']);
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where('name', 'like', '%'.$keyword.'%');
            }
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create($data){
        return $this->model->create($data);
    }

    public function getByID($id){
        return $this->model->with(['permissions'])->where('id', $id)->first();
    }

    public function updateByID($id,$data){
        return $this->model->find($id)->update($data);
    }

    public function destroyByIDs($arrID){
        return $this->model->destroy($arrID);
    }

    public function getAll(){
        return $this->model->with(['permissions'])->get();
    }
}
