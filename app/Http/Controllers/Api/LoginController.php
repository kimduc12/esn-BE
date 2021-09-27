<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Constants\UserConst;
use App\Services\UserService;
use Socialite;
class LoginController extends RestfulController
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    /**
     * Login
     * @group Login management
     *
     * @bodyParam loginName string required . Example: admin
     * @bodyParam password string required . Example: 123456
     * @response {
        *   "status": true,
        *   "message": "Login success",
        *   "data": {
                *   "id": 1,
                *   "name": "Mr A",
                *   "email": "mra@yopmail.com",
                *   "accessToken": "eyJ0eXAiOiJKV1QiLCJh"
            * }
        * }
     * @response status=200 scenario="Wrong email and password" {
        *  "status": false,
        *  "message": "Login failed"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function login(Request $request){
        $this->validate($request, [
            'loginName' => 'bail|required',
            'password' => 'required|min:6|max:20',
        ]);

        try{
            $loginName = $request->input('loginName');
            $password = $request->input('password');
            $flag = false;
            if (
                Auth::attempt(['email'=> $loginName,'password'=> $password, 'status'=> UserConst::STATUS_ACTIVE]) ||
                Auth::attempt(['username'=> $loginName,'password'=> $password, 'status'=> UserConst::STATUS_ACTIVE]) ||
                Auth::attempt(['phone'=> $loginName,'password'=> $password, 'status'=> UserConst::STATUS_ACTIVE])
            ) {
                $flag = true;
            }
            if($flag == false){
                return $this->_error('Login failed');
            }
            $user = Auth::user();
            $this->_customUserData($user);
            return $this->_response($user, trans('login.login_success'));
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Login by email and password
     * @group Login management
     *
     * @bodyParam email email required . Example: mra@yopmail.com
     * @bodyParam password string required . Example: 123456
     * @response {
        *   "status": true,
        *   "message": "Login success",
        *   "data": {
                *   "id": 1,
                *   "name": "Mr A",
                *   "email": "mra@yopmail.com",
                *   "accessToken": "eyJ0eXAiOiJKV1QiLCJh"
            * }
        * }
     * @response status=200 scenario="Wrong email and password" {
        *  "status": false,
        *  "message": "Login failed"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function loginByEmail(Request $request){
        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'required|min:6|max:20',
        ]);

        try{
            $email = $request->input('email');
            $password = $request->input('password');
            if (Auth::attempt(['email'=>$email,'password'=>$password, 'status'=>UserConst::STATUS_ACTIVE])) {
                $user = Auth::user();
                $this->_customUserData($user);
                return $this->_response($user, trans('login.login_success'));
            }else{
                return $this->_error('Login failed');
            }
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
    /**
     * Login by username and password
     * @group Login management
     *
     * @bodyParam username string required . Example: superadmin
     * @bodyParam password string required . Example: 123
     * @response {
        *   "status": true,
        *   "message": "Login success",
        *   "data": {
                *   "id": 1,
                *   "name": "admin",
                *   "accessToken": "eyJ0eXAiOiJKV1QiLCJh"
            * }
        * }
     * @response status=200 scenario="Wrong email and password" {
        *  "status": false,
        *  "message": "Login failed"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function loginByUsername(Request $request){
        $this->validate($request, [
            'username' => 'bail|required',
            'password' => 'required|min:6|max:20',
        ]);
        try{
            $username = $request->input('username');
            $password = $request->input('password');
            if (Auth::attempt(['username'=>$username,'password'=>$password, 'status'=>UserConst::STATUS_ACTIVE])) {
                $user = Auth::user();
                $this->_customUserData($user, 'JWT-USERNAME');
                return $this->_response($user, trans('login.login_success'));
            }else{
                return $this->_error(trans('login.login_failed'));
            }
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Login by facebook
     * @group Login management
     *
     * @bodyParam token string required . Example: {token}
     * @response {
        *   "status": true,
        *   "message": "Login success",
        *   "data": {
                *   "id": 1,
                *   "name": "mr facebook",
                *   "roles": [
                    *   {
                        *   "id": 2,
                        *   "name": "Customer"
                    *   }
                *   ],
                *   "accessToken": "eyJ0eXAiOiJKV1QiLCJh"
            * }
        * }
     * @response status=200 scenario="Wrong token" {
        *  "status": false,
        *  "message": "Login failed"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function loginByFacebook(Request $request){
        $this->validate($request, [
            'token' => 'required',
        ]);
        try{
            $token = $request->input('token');
            $facebookUser = Socialite::driver('facebook')->userFromToken($token);
            $user = $this->userService->createByFacebook($facebookUser);
            if(!$user){
                return $this->_error(trans('login.login_failed'));
            }
            $this->_customUserData($user, 'JWT-FACEBOOK');
            return $this->_response($user, trans('login.login_success'));

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Login by google
     * @group Login management
     *
     * @bodyParam token string required . Example: {token}
     * @response {
        *   "status": true,
        *   "message": "Login success",
        *   "data": {
                *   "id": 1,
                *   "name": "mr google",
                *   "roles": [
                    *   {
                        *   "id": 2,
                        *   "name": "Customer"
                    *   }
                *   ],
                *   "accessToken": "eyJ0eXAiOiJKV1QiLCJh"
            * }
        * }
     * @response status=200 scenario="Wrong token" {
        *  "status": false,
        *  "message": "Login failed"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function loginByGoogle(Request $request){
        $this->validate($request, [
            'token' => 'required',
        ]);
        try{
            $token = $request->input('token');
            $googleUser = Socialite::driver('google')->userFromToken($token);
            $user = $this->userService->createByGoogle($googleUser);
            if(!$user){
                return $this->_error(trans('login.login_failed'));
            }
            $this->_customUserData($user, 'JWT-GOOGLE');
            return $this->_response($user, trans('login.login_success'));

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Login by zalo
     * @group Login management
     *
     * @bodyParam code string required . Example: {code}
     * @response {
        *   "status": true,
        *   "message": "Login success",
        *   "data": {
                *   "name": "Đức",
                *   "status": 1,
                *   "updated_at": "2021-03-26T16:35:47.000000Z",
                *   "created_at": "2021-03-26T16:35:47.000000Z",
                *   "id": 36,
                *   "roles": [
                    *   {
                        *   "id": 2,
                        *   "name": "Customer"
                    *   }
                *   ],
                *   "accessToken": "eyJ0eXAiOiJKV1QiLCJh"
            * }
        * }
     * @response status=200 scenario="Wrong token" {
        *  "status": false,
        *  "message": "Login failed"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function loginByZalo(Request $request){
        $this->validate($request, [
            'code' => 'required',
        ]);
        try{
            $code = $request->input('code');
            $user = $this->userService->loginByZalo($code);
            if(!$user){
                return $this->_error(trans('login.login_failed'));
            }
            $this->_customUserData($user, 'JWT-ZALO');
            return $this->_response($user, trans('login.login_success'));

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    private function _customUserData(&$user, $token_name = 'JWT-EMAIL'){
        $user->roles = $user->roles()->with('permissions')->get();
        $user->accessToken = $user->createToken($token_name)->accessToken;
    }
}
