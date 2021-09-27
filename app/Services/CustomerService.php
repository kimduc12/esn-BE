<?php
namespace App\Services;

use App\Constants\RolePermissionConst;
use App\Constants\UserConst;
use App\Repositories\UserInterface;
use App\Repositories\RoleInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CustomerService extends BaseService {
    protected $user;
    function __construct(
        UserInterface $user,
        RoleInterface $role
    ){
        $this->user = $user;
        $this->role = $role;
    }

    public function getListPaginate($perPage = 20,$filter){
        return $this->user->getCustomerAllPaginate($perPage, $filter);
    }

    public function create($data){
        $data['status'] = UserConst::STATUS_ACTIVE;
        if(isset($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }
        if(isset($data['notes'])){
            $notes = $data['notes'];
            unset($data['notes']);
        }
        $customer = $this->user->create($data);
        if(!$customer){
            return $this->_result(false, trans('messages.created_failed'));
        }
        $customer->assignRole(RolePermissionConst::ROLE_CUSTOMER);

        $dataCustomer = [
            'customer_id' => $customer->id,
        ];
        if(isset($notes)){
            $dataCustomer['notes'] = $notes;
        }
        $customer->customer_info()->create($dataCustomer);
        return $this->_result(true, trans('messages.created_success'));
    }

    public function getByID($id){
        $user = $this->user->getCustomerByID($id);
        if(!$user){
            return $this->_result(false, trans('messages.not_found'));
        }
        $user->load(['city', 'district', 'ward']);
        return $this->_result(true, '', $user);
    }

    public function updateByID($id, $data){
        $customer = $this->user->getUserByID($id);
        if(!$customer){
            return $this->_result(false, trans('messages.not_found'));
        }
        if(isset($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }
        if(isset($data['email']) && $customer->email != $data['email']){
            $check_user = $this->user->getByEmail($data['email']);
            if($check_user){
                return $this->_result(false, trans('messages.your_email_has_already_taken'));
            }
        }
        if(isset($data['notes'])){
            $notes = $data['notes'];
            unset($data['notes']);
        }
        $result = $this->user->updateUserByID($id, $data);
        if(!$result){
            return $this->_result(false, trans('messages.update_failed'));
        }

        $dataCustomer = [];
        if(isset($notes)){
            $dataCustomer['notes'] = $notes;
        }
        if(!empty($dataCustomer)) {
            $customer->customer_info()->update($dataCustomer);
        }
        return $this->_result(true, trans('messages.update_success'));
    }

    public function destroyByIDs($ids){
        $check = $this->user->destroyCustomersByIDs($ids);
        if(!$check){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function getMyFavouriteProducts($perPage = 20, $filter){
        return $this->user->getMyFavouriteProductsPaginate($perPage, $filter);
    }

    public function getAllMyFavouriteProducts(){
        return $this->user->getAllMyFavouriteProducts();
    }

    public function addMyFavouriteProduct($data){
        $product_ids = $data['product_ids'];
        $user = Auth::user();
        foreach ($product_ids as $key => $product_id) {
            $row = $user->favourite_products()->where('product_id', $product_id)->first();
            if ($row) {
                unset($product_ids[$key]);
            }
        }
        $user->favourite_products()->attach($product_ids);
        return true;
    }

    public function removeMyFavouriteProduct($data){
        $product_ids = $data['product_ids'];
        $user = Auth::user();
        foreach ($product_ids as $key => $product_id) {
            $row = $user->favourite_products()->where('product_id', $product_id)->first();
            if (!$row) {
                unset($product_ids[$key]);
            }
        }
        $user->favourite_products()->detach($product_ids);
        return true;
    }

    public function getMyReadProducts($perPage = 20, $filter){
        return $this->user->getMyReadProductsPaginate($perPage, $filter);
    }

    public function getAllMyReadProducts(){
        return $this->user->getAllMyReadProducts();
    }

    public function addMyReadProduct($data){
        $product_ids = $data['product_ids'];
        $user = Auth::user();
        foreach ($product_ids as $key => $product_id) {
            $row = $user->read_products()->where('product_id', $product_id)->first();
            if ($row) {
                unset($product_ids[$key]);
            }
        }
        $user->read_products()->attach($product_ids);
        return true;
    }
}
