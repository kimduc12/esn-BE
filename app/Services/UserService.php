<?php
namespace App\Services;

use App\Constants\RolePermissionConst;
use App\Constants\UserConst;
use App\Mail\ForgetPassword;
use App\Repositories\UserInterface;
use App\Repositories\RoleInterface;
use App\Repositories\GiftExchangeInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserService extends BaseService {
    protected $user;
    protected $zalo;
    protected $giftExchange;
    function __construct(
        ZaloService $zalo,
        UserInterface $user,
        RoleInterface $role,
        GiftExchangeInterface $giftExchange
    ){
        $this->zalo = $zalo;
        $this->user = $user;
        $this->role = $role;
        $this->giftExchange = $giftExchange;
    }

    public function getUserByPhone($phone){
        return $this->user->getUserByPhone($phone);
    }

    public function registerByEmail($data){
        $data['status'] = UserConst::STATUS_ACTIVE;
        $user = $this->user->createByEmail($data);
        $user->assignRole(RolePermissionConst::ROLE_CUSTOMER);
        $user->customer_info()->create([
            'customer_id' => $user->id,
        ]);
        return $user;
    }

    public function createByFacebook($facebookUser){
        $user = false;
        if(isset($facebookUser['email']) && !empty($facebookUser['email'])){
            $userGetByFacebookEmail = $this->user->getByEmail($facebookUser['email']);
            if(!$userGetByFacebookEmail){
                $userGetByFacebookID = $this->user->getByFacebookID($facebookUser['id']);
                if(!$userGetByFacebookID){
                    $user = $this->user->create([
                        'name' => $facebookUser['name'] ?? '',
                        'email' => $facebookUser['email'],
                        'status' => UserConst::STATUS_ACTIVE
                    ]);
                    $user->assignRole(RolePermissionConst::ROLE_CUSTOMER);
                    $user->customer_info()->create([
                        'customer_id' => $user->id,
                    ]);
                    $user->user_providers()->create([
                        'provider_id' => $facebookUser['id'],
                        'provider' => UserConst::PROVIDER_FACEBBOK,
                    ]);
                }else{
                    $user = $userGetByFacebookID;
                    $user->forceFill([
                        'email' => $facebookUser['email']
                    ])->save();
                }
            }else{
                $user = $userGetByFacebookEmail;
            }
        }else if( isset($facebookUser['id']) && !empty($facebookUser['id'])){
            $userGetByFacebookID = $this->user->getByFacebookID($facebookUser['id']);
            if(!$userGetByFacebookID){
                $user = $this->user->create([
                    'name' => $facebookUser['name'] ?? '',
                    'status' => UserConst::STATUS_ACTIVE
                ]);
                $user->assignRole(RolePermissionConst::ROLE_CUSTOMER);
                $user->customer_info()->create([
                    'customer_id' => $user->id,
                ]);
                $user->user_providers()->create([
                    'provider_id' => $facebookUser['id'],
                    'provider' => UserConst::PROVIDER_FACEBBOK,
                ]);
            }else{
                $user = $userGetByFacebookID;
            }
        }
        return $user;
    }

    public function createByGoogle($googleUser){
        $user = false;
        if(isset($googleUser['email']) && !empty($googleUser['email'])){
            $userGetByGoogleEmail = $this->user->getByEmail($googleUser['email']);
            if(!$userGetByGoogleEmail){
                $userGetByGoogleID = $this->user->getByGoogleID($googleUser['id']);
                if(!$userGetByGoogleID){
                    $user = $this->user->create([
                        'name' => $googleUser['name'] ?? '',
                        'email' => $googleUser['email'],
                        'status' => UserConst::STATUS_ACTIVE
                    ]);
                    $user->assignRole(RolePermissionConst::ROLE_CUSTOMER);
                    $user->customer_info()->create([
                        'customer_id' => $user->id,
                    ]);
                    $user->user_providers()->create([
                        'provider_id' => $googleUser['id'],
                        'provider' => UserConst::PROVIDER_GOOGLE,
                    ]);
                }else{
                    $user = $userGetByGoogleID;
                    $user->forceFill([
                        'email' => $googleUser['email']
                    ])->save();
                }
            }else{
                $user = $userGetByGoogleEmail;
            }

        }else if( isset($googleUser['id']) && !empty($googleUser['id'])){
            $userGetByGoogleID = $this->user->getByGoogleID($googleUser['id']);
            if(!$userGetByGoogleID){
                $user = $this->user->create([
                    'name' => $googleUser['name'] ?? '',
                    'status' => UserConst::STATUS_ACTIVE
                ]);
                $user->assignRole(RolePermissionConst::ROLE_CUSTOMER);
                $user->customer_info()->create([
                    'customer_id' => $user->id,
                ]);
                $user->user_providers()->create([
                    'provider_id' => $googleUser['id'],
                    'provider' => UserConst::PROVIDER_GOOGLE,
                ]);
            }else{
                $user = $userGetByGoogleID;
            }
        }
        return $user;
    }

    public function loginByZalo($code){
        $response = $this->zalo->getAccessToken($code);
        $tokenResponse = $response->json();
        \Log::info("loginByZalo getAccessToken", [$tokenResponse]);
        if (isset($response['error_name'])) {
            return false;
        }
        $response = $this->zalo->getUserInfoByAccessToken($tokenResponse['access_token']);
        if ($response->failed() == true) {
            return false;
        }
        $zaloUser = $response->json();

        $userGetByZaloID = $this->user->getByZaloID($zaloUser['id']);
        if($userGetByZaloID){
            return $userGetByZaloID;
        }
        $user = $this->user->create([
            'name' => $zaloUser['name'] ?? '',
            'status' => UserConst::STATUS_ACTIVE
        ]);
        $user->assignRole(RolePermissionConst::ROLE_CUSTOMER);
        $user->customer_info()->create([
            'customer_id' => $user->id,
        ]);
        $user->user_providers()->create([
            'provider_id' => $zaloUser['id'],
            'provider' => UserConst::PROVIDER_ZALO,
        ]);
        return $user;
    }

    public function getListPaginate($perPage = 20,$filter){
        return $this->user->getListPaginate($perPage, $filter);
    }

    public function getCustomerAllPaginate($perPage = 20,$filter){
        return $this->user->getCustomerAllPaginate($perPage,$filter);
    }

    public function create($data){
        $data['status'] = UserConst::STATUS_ACTIVE;
        $data['password'] = Hash::make($data['password']);
        $role_id = $data['role_id'];
        unset($data['role_id']);
        unset($data['confirm_password']);
        $user = $this->user->create($data);
        if(!$user){
            return $this->_result(false, trans('messages.created_failed'));
        }
        $role = $this->role->getByID($role_id);
        $user->assignRole($role->name);
        return $this->_result(true, trans('messages.created_success'));
    }

    public function createNewCustomer($data){
        $data['status'] = UserConst::STATUS_ACTIVE;
        $data['password'] = Hash::make($data['password']);
        $user = $this->user->create($data);
        if(!$user){
            return $this->_result(false, trans('messages.created_failed'));
        }
        $user->assignRole(RolePermissionConst::ROLE_CUSTOMER);
        return $this->_result(true, trans('messages.created_success'));
    }

    public function getUserByID($id){
        $user = $this->user->getUserByID($id);
        if(!$user){
            return $this->_result(false, trans('messages.not_found'));
        }
        return $this->_result(true, '', $user);
    }

    public function updateUserByID($id, $data){
        $user = $this->user->getUserByID($id);
        if(!$user){
            return $this->_result(false, trans('messages.not_found'));
        }
        if(isset($data['role_id'])){
            $role_id = $data['role_id'];
            unset($data['role_id']);
        }
        if(isset($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }
        if(isset($data['email']) && $user->email != $data['email']){
            $check_user = $this->user->getByEmail($data['email']);
            if($check_user){
                return $this->_result(false, trans('messages.your_email_has_already_taken'));
            }
        }
        $result = $this->user->updateUserByID($id, $data);
        if(!$result){
            return $this->_result(false, trans('messages.update_failed'));
        }
        if(isset($role_id)){
            $role = $this->role->getByID($role_id);
            $user->syncRoles([$role->name]);
        }
        return $this->_result(true, trans('messages.update_success'));
    }

    public function destroyUsersByIDs($ids){
        $check = $this->user->destroyUsersByIDs($ids);
        if(!$check){
            return $this->_result(false, trans('messages.cannot_delete'));
        }
        return $this->_result(true, trans('messages.deleted_success'));
    }

    public function updateMyInfo($data){
        $user = Auth::user();
        if(isset($data['password'])){
            if (!isset($data['old_password']) || !Hash::check($data['old_password'], $user->password)) {
                return $this->_result(false, trans('messages.old_password_wrong'));
            }
            $data['password'] = Hash::make($data['password']);
            unset($data['old_password']);
        }
        if(isset($data['email']) && $user->email != $data['email']){
            $check_user = $this->user->getByEmail($data['email']);
            if($check_user){
                return $this->_result(false, trans('messages.your_email_has_already_taken'));
            }
        }
        $result = $this->user->updateUserByID($user->id, $data);
        if(!$result){
            return $this->_result(false, trans('messages.update_failed'));
        }
        return $this->_result(true, trans('messages.update_success'));
    }

    public function lockByID($id){
        $user = $this->user->getUserByID($id);
        if(!$user){
            return $this->_result(false, trans('messages.not_found'));
        }
        $data = [
            'status' => UserConst::STATUS_LOCK
        ];
        $this->user->updateUserByID($id, $data);
        return $this->_result(true, trans('messages.update_success'));
    }

    public function unlockByID($id){
        $user = $this->user->getUserByID($id);
        if(!$user){
            return $this->_result(false, trans('messages.not_found'));
        }
        $data = [
            'status' => UserConst::STATUS_ACTIVE
        ];
        $this->user->updateUserByID($id, $data);
        return $this->_result(true, trans('messages.update_success'));
    }

    public function getHistoryExchangedGifts($perPage = 20, $filter = []){
        $user = Auth::user();
        $filter['user_id'] = $user->id;
        return $this->giftExchange->getAllPaginate($perPage, $filter);
    }

    public function forgetPassword($data){
        $token = Str::random(64);
        $email = $data['email'];
        DB::table('password_resets')->where('email', $email)->delete();
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => bcrypt($token),
            'created_at' => Carbon::now()
        ]);

        Mail::to($email)->send(new ForgetPassword($email, $token));

        return $this->_result(true, trans('passwords.sent'));
    }
}
