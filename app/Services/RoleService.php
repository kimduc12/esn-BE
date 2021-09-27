<?php
namespace App\Services;

use App\Repositories\RoleInterface;
use App\Repositories\UserInterface;

class RoleService extends BaseService {
    protected $role;
    protected $user;
    function __construct(
        RoleInterface $role,
        UserInterface $user
    ){
        $this->role = $role;
        $this->user = $user;
    }

    public function getAllPaginate($perPage = 20, $filter = []){
        return $this->role->getAllPaginate($perPage, $filter);
    }

    public function create($data){
        $permission_ids = $data['permission_ids'];
        unset($data['permission_ids']);
        $role = $this->role->create($data);
        $role->givePermissionTo($permission_ids);
        return $role;
    }

    public function getByID($id){
        return $this->role->getByID($id);
    }

    public function updateByID($id,$data){
        $permission_ids = $data['permission_ids'];
        unset($data['permission_ids']);
        $role = $this->role->getByID($id);
        if(!$role){
            return $this->_result(false,  trans('messages.not_found'));
        }
        $this->role->updateByID($id, $data);
        $role->syncPermissions($permission_ids);
        return $this->_result(true,  trans('messages.update_success'));
    }

    public function destroyByIDs($arrID){
        $users = $this->user->getByRoleIDs($arrID);
        if ($users->isNotEmpty()){
            return $this->_result(false, trans('messages.cannot_delete_by_user_has_role'));
        }
        $result =$this->role->destroyByIDs($arrID);
        if(!$result){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getAll($filter = []){
        return $this->role->getAll($filter);
    }
}
