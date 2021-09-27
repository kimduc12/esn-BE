<?php
namespace App\Repositories\Eloquent;

use App\Constants\CustomerConst;
use App\Constants\ProductConst;
use App\Constants\RolePermissionConst;
use App\Repositories\UserInterface;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use App\Constants\UserConst;
use Illuminate\Support\Facades\Auth;

class UserRepository implements UserInterface {
    protected $model;
    function __construct(User $user){
        $this->model = $user;
    }

    public function getByEmail($email){
        return $this->model->whereEmail($email)->first();
    }

    public function getByFacebookID($facebook_id){
        return $this->model->whereHas('user_providers', function($q) use ($facebook_id) {
            $q->whereProviderId($facebook_id)->whereProvider('facebook');
        })->first();
    }

    public function getByGoogleID($google_id){
        return $this->model->whereHas('user_providers', function($q) use ($google_id) {
            $q->whereProviderId($google_id)->whereProvider('google');
        })->first();
    }

    public function getByZaloID($zalo_id){
        return $this->model->whereHas('user_providers', function($q) use ($zalo_id) {
            $q->whereProviderId($zalo_id)->whereProvider('zalo');
        })->first();
    }

    public function getUserByPhone($phone){
        return $this->model->wherePhone($phone)->first();
    }

    public function createByEmail($data){
        $user = new User();
        $user->name = $data['name'];
        if(isset($data['username'])){
            $user->username = $data['username'];
        }
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->phone = $data['phone'];
        if(isset($data['gender'])){
            $user->gender = $data['gender'];
        }
        if(isset($data['birthday'])){
            $user->birthday = $data['birthday'];
        }
        if(isset($data['address'])){
            $user->address = $data['address'];
        }
        if(!isset($data['status'])){
            $user->status = UserConst::STATUS_UNACTIVE;
        }else{
            $user->status = $data['status'];
        }
        $user->save();
        return $user;
    }

    public function getListPaginate($perPage = 20, $filter=[]){
        $query = $this->model->with('roles');
        $userRoles = RolePermissionConst::MANAGEMENT_ROLES;
        $query = $query->where('id', '!=', 1);
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where(function ($q) use ($keyword, $filter) {
                    $q->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('email', 'like', '%'.$keyword.'%')
                        ->orWhere('phone', 'like', '%'.$keyword.'%');
                });
            }
            if(isset($filter['status']) && $filter['status'] != '' ){
                $query = $query->where('status', $filter['status']);
            }
            if(isset($filter['gender']) && $filter['gender'] != '' ){
                $query = $query->where('gender', $filter['gender']);
            }
            if(isset($filter['role_id']) && $filter['role_id']!=0){
                $role_id = $filter['role_id'];
                $query = $query->whereHas('roles', function ($query) use ($role_id) {
                    return $query->where('id', $role_id);
                });
            }
        }

        $query = $query->whereHas('roles', function ($query) use ($userRoles) {
            return $query->whereIn('name', $userRoles);
        });

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getCustomerAllPaginate($perPage = 20,$filter=[]){
        $query = $this->model->with(['customer_info']);
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where(function ($q) use ($keyword, $filter) {
                    $q->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('email', 'like', '%'.$keyword.'%')
                        ->orWhere('phone', 'like', '%'.$keyword.'%');
                });
            }
            if(isset($filter['status']) && $filter['status'] != '' ){
                $query = $query->where('status', $filter['status']);
            }
            if(isset($filter['gender']) && $filter['gender'] != '' ){
                $query = $query->where('gender', $filter['gender']);
            }
            if(isset($filter['is_subscribe']) && $filter['is_subscribe'] != '' ){
                $query = $query->where('is_subscribe', $filter['is_subscribe']);
            }
            if(isset($filter['is_loyal']) && $filter['is_loyal'] != '' ){
                $is_loyal = $filter['is_loyal'];
                $query = $query->whereHas('customer_info', function($query) use ($is_loyal) {
                    if(!$is_loyal) {
                        $query->where('badge', 0);
                    } else {
                        $query->where('badge', '>=', CustomerConst::BADGE_BRONZE);
                    }
                });
            }
        }
        $query = $query->role(RolePermissionConst::ROLE_CUSTOMER);
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create($data){
        return $this->model->create($data);
    }

    public function getUserByID($id){
        return $this->model->with('roles')->find($id);
    }

    public function getCustomerByID($id){
        return $this->model->with('customer_info')->find($id);
    }

    public function updateUserByID($id, $data){
        return $this->model->where('id', $id)->update($data);
    }

    public function destroyUsersByIDs($ids){
        return $this->model->destroy($ids);
    }

    public function destroyCustomersByIDs($ids){
        return $this->model
                ->role(RolePermissionConst::ROLE_CUSTOMER)
                ->whereIn('id', $ids)
                ->delete();
    }

    public function getBySimilarPhone($phone){
        return $this->model->where('phone', 'like', '%'.$phone.'%')->get();
    }

    public function getBySimilarEmail($email){
        return $this->model->where('email', 'like', '%'.$email.'%')->get();
    }

    public function getByRoleIDs($role_ids) {
        $query = $this->model;
        $query = $query->whereHas('roles', function ($query) use ($role_ids) {
            return $query->whereIn('id', $role_ids);
        });
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getMyFavouriteProductsPaginate($perPage = 20, $filter=[]){
        $user = Auth::user();
        $query = $user->favourite_products();
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%'.$keyword.'%');
                });
            }
        }
        $query = $query->where('status', ProductConst::STATUS_ACTIVE);
        return $query->paginate($perPage);
    }

    public function getAllMyFavouriteProducts(){
        $user = Auth::user();
        $query = $user->favourite_products();
        $query = $query->where('status', ProductConst::STATUS_ACTIVE);
        return $query->get();
    }

    public function getMyReadProductsPaginate($perPage = 20, $filter=[]){
        $user = Auth::user();
        $query = $user->read_products();
        if(!empty($filter)){
            if(isset($filter['keyword']) && $filter['keyword'] !=''){
                $keyword = $filter['keyword'];
                $query = $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', '%'.$keyword.'%');
                });
            }
        }
        $query = $query->where('status', ProductConst::STATUS_ACTIVE);
        return $query->paginate($perPage);
    }

    public function getAllMyReadProducts(){
        $user = Auth::user();
        $query = $user->read_products();
        $query = $query->where('status', ProductConst::STATUS_ACTIVE);
        return $query->get();
    }
}
