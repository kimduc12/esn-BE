<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Repositories\UserInterface;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends RestfulController
{
    protected $userService;
    protected $user;
    public function __construct(UserService $userService, UserInterface $userInterface){
        parent::__construct();
        $this->userService = $userService;
        $this->user = $userInterface;
    }

    /**
     * Get all users with paginate
     * @group User management
     * @authenticated
     *
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam status integer Field to filter. Defaults to null.
     * @queryParam gender integer Field to filter. Defaults to null.
     * @queryParam role_id integer Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "users": {
                *   "id": 1,
                *   "name": "Mr A",
                *   "email": "mra@yopmail.com",
                *   "accessToken": "eyJ0eXAiOiJKV1QiLCJh"
            * }
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function index(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $keyword = $request->input('keyword', '');
            $status  = $request->input('status', '');
            $gender  = $request->input('gender', '');
            $role_id = $request->input('role_id', '');
            $filter = [
                'keyword' => $keyword,
                'status'  => $status,
                'gender'  => $gender,
                'role_id' => $role_id,
            ];

            $users = $this->userService->getListPaginate($perPage, $filter);

            $users->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($users);
            $pagingArr = $users->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'users' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Register a client
     * @group User management
     *
     * @bodyParam name string required Fullname. Example: Mr A
     * @bodyParam email email required . Example: mra@yopmail.com
     * @bodyParam password string required . Example: 123
     * @bodyParam phone string required . Example: 0915182436
     * @bodyParam gender boolean required . Example: 0
     * @bodyParam birthday date required . Example: 2012-12-30
     * @bodyParam is_subscribe boolean required . Example: 0
     * @response {
        *   "status": true,
        *   "message": "Register success",
        *   "data": {
                *   "id": 1,
                *   "name": "Mr A",
                *   "email": "mra@yopmail.com",
                *   "accessToken": "eyJ0eXAiOiJKV1QiLCJh"
            * }
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     * */
    public function register(Request $request){
        $this->validate($request, [
            'name' => 'bail|required',
            'email' => 'bail|required|email|unique:users,email',
            'password' => 'bail|required|min:6|max:20',
            'phone' => 'bail|required|unique:users,phone',
            'gender' => 'bail|required|boolean',
            'birthday' => 'bail|required|date_format:Y-m-d',
            'is_subscribe' => 'bail|required|boolean',
        ], [
            'name.required' => trans('validate.required', ['attribute' => 'Họ tên']),
            'email.required' => trans('validate.required', ['attribute' => 'Email']),
            'email.email' => trans('validate.email', ['attribute' => 'Email']),
            'email.unique' => trans('validate.unique', ['attribute' => 'Email']),
            'password.required' => trans('validate.required', ['attribute' => 'Mật khẩu']),
            'password.min' => trans('validate.min', ['attribute' => 'Mật khẩu', 'value' => 6]),
            'password.max' => trans('validate.max', ['attribute' => 'Mật khẩu', 'value' => 20]),
            'phone.required' => trans('validate.required', ['attribute' => 'Số điện thoại']),
            'phone.unique' => trans('validate.unique', ['attribute' => 'Số điện thoại']),
            'gender.required' => trans('validate.required', ['attribute' => 'Giới tính']),
            'gender.boolean' => trans('validate.boolean', ['attribute' => 'Giới tính']),
            'birthday.required' => trans('validate.required', ['attribute' => 'Ngày sinh']),
            'birthday.date_format' => trans('validate.date_format', ['attribute' => 'Ngày sinh']),
            'is_subscribe.required' => trans('validate.required', ['attribute' => 'Đăng ký']),
            'is_subscribe.boolean' => trans('validate.boolean', ['attribute' => 'Đăng ký']),
        ]);
        try{
            $requestData = $request->all();
            $user = $this->userService->registerByEmail($requestData);
            $this->_customUserData($user);
            return $this->_response($user, 'Register success');
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    private function _customUserData(&$user, $token_name = 'JWT-VBA-EMAIL'){
        $user->roles = $user->roles()->with('permissions')->get();
        $user->accessToken = $user->createToken($token_name, explode(' ', $user->scopes))->accessToken;
    }

    /**
    * Check email exist in DB
    * @group User management
    *
     * @bodyParam email email required . Example: mra@yopmail.com
     * @response {
        *   "result": true
        * }
     * @response status=200 scenario="Not found" {
        *  "result": false
        * }
    **/
    public function checkEmail(Request $request){
        $this->validate($request, [
            'email' => 'bail|required|email',
        ]);
        try{
            $email = $request->input('email');
            $checkEmail = $this->user->getByEmail($email);
            if(!$checkEmail){
                return $this->_response(['result'=> false]);
            }
            return $this->_response(['result'=> true]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
    * Check phone exist in DB
    * @group User management
    *
    * @bodyParam phone string required . Example: 0915182436
     * @response {
        *   "result": true
        * }
     * @response status=200 scenario="Not found" {
        *  "result": false
        * }
    */
    public function checkPhone(Request $request){
        $this->validate($request, [
            'phone' => 'bail|required',
        ]);
        try{
            $phone = $request->input('phone');
            $checkPhone = $this->user->getUserByPhone($phone);
            if(!$checkPhone){
                return $this->_response(['result'=> false]);
            }
            return $this->_response(['result'=> true]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a user
     * @group User management
     * @authenticated
     *
     * @bodyParam name string required Fullname. Example: Mr A
     * @bodyParam username string . Example: Mr A
     * @bodyParam email email required . Example: mra@yopmail.com
     * @bodyParam password string required . Example: 123
     * @bodyParam phone string . Example: 0915182436
     * @bodyParam gender boolean . Example: 0
     * @bodyParam birthday date . Example: 2012-12-30
     * @bodyParam role_id integer required . Example: 1
     * @response {
        *   "status": true,
        *   "message": "Created"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function store(Request $request){
        $this->validate($request, [
            'name'         => 'bail|required',
            'username'     => 'bail|nullable|min:6|max:20|unique:users,username',
            'email'        => 'bail|required|email|unique:users,email',
            'password'     => 'bail|required|min:6|max:20',
            'phone'        => 'bail|nullable|unique:users,phone',
            'gender'       => 'bail|nullable|boolean',
            'birthday'     => 'bail|nullable|date_format:Y-m-d',
            'role_id'      => 'bail|required|exists:roles,id',
        ]);
        try{
            $data = $request->all();
            $result = $this->userService->create($data);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get a user by id
     * @group User management
     * @authenticated
     *
     * @urlParam id integer required.
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Mr A",
                *   "email": "mra@yopmail.com"
            * }
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function show($id){
        try{
            $result = $this->userService->getUserByID($id);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Update a user by user id
     * @group User management
     * @authenticated
     *
     * @urlParam id integer required.
     * @bodyParam name string required Fullname. Example: Mr A
     * @bodyParam username string . Example: Mr A
     * @bodyParam email email required . Example: mra@yopmail.com
     * @bodyParam password string . Example: 123
     * @bodyParam phone string . Example: 0915182436
     * @bodyParam gender boolean . Example: 0
     * @bodyParam birthday date . Example: 2012-12-30
     * @bodyParam role_id integer . Example: 1
     * @response {
        *   "status": true,
        *   "message": "Updated"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     * @return mixed
     */
    public function update(Request $request, $id){
        $this->validate($request, [
            'name'         => 'bail|required',
            'username'     => 'bail|nullable|min:6|max:20|unique:users,username,'.$id,
            'email'        => 'bail|required|email|unique:users,email,'.$id,
            'password'     => 'bail|nullable|min:6|max:20',
            'phone'        => 'bail|nullable|unique:users,phone,'.$id,
            'gender'       => 'bail|nullable|boolean',
            'birthday'     => 'bail|nullable|date_format:Y-m-d',
            'role_id'      => 'bail|nullable|exists:roles,id',
        ]);
        try{
            $data = $request->all();
            $result = $this->userService->updateUserByID($id, $data);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Delete a list of users by an array of user id
     * @group User management
     * @authenticated
     *
     * @bodyParam ids integer[] required.
     * @response {
        *   "status": true,
        *   "message": "Deleted"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function destroy(Request $request){
        $this->validate($request, [
            'ids' => 'required|array|min:1',
        ]);
        try{
            $ids = $request->input('ids');
            $result = $this->userService->destroyUsersByIDs($ids);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get info of auth user
     * @group User management
     * @authenticated
     *
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "Mr A",
                *   "email": "mra@yopmail.com"
            * }
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function getMyInfo() {
        try{
            $user = Auth::user();
            $this->_customUserData($user);
            return $this->_response($user);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Update my info
     * @group User management
     * @authenticated
     *
     * @bodyParam name string required Fullname. Example: Mr A
     * @bodyParam email email required . Example: mra@yopmail.com
     * @bodyParam password string . Example: 123
     * @bodyParam phone string required . Example: 0915182436
     * @bodyParam gender boolean required . Example: 0
     * @bodyParam birthday date required . Example: 2012-12-30
     * @bodyParam is_subscribe boolean required . Example: 0
     * @response {
        *   "status": true,
        *   "message": "Update success",
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function updateMyInfo(Request $request){
        $user = Auth::user();
        $id = $user->id;
        $this->validate($request, [
            'name'         => 'bail|required',
            'username'     => 'bail|nullable|unique:users,username,'.$id,
            'email'        => 'bail|required|email|unique:users,email,'.$id,
            'password'     => 'bail|nullable|min:6|max:20',
            'phone'        => 'bail|required|unique:users,phone,'.$id,
            'gender'       => 'bail|required|boolean',
            'birthday'     => 'bail|required|date_format:Y-m-d',
            'is_subscribe' => 'bail|required|boolean',
        ]);
        try{
            $data = $request->all();
            $result = $this->userService->updateMyInfo($data);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    public function download(){
        try{
            return Excel::download(new ExportsCustomer(), 'customer-'.date('Y-m-d').'.xlsx');
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Lock a user by user id
     * @group User management
     * @authenticated
     *
     * @urlParam id integer required.
     * @response {
        *   "status": true,
        *   "message": "Lock successfully"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     * @return mixed
     */
    public function lock($id){
        try{
            $result = $this->userService->lockByID($id);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Unlock a user by user id
     * @group User management
     * @authenticated
     *
     * @urlParam id integer required.
     * @response {
        *   "status": true,
        *   "message": "Unlock successfully"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     * @return mixed
     */
    public function unlock($id){
        try{
            $result = $this->userService->unlockByID($id);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get my exchanged gifts with paginate
     * @group User management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "gifts": [
                *   {
                    *   "id": 1,
                    *   "user_id": 1,
                    *   "gift_id": 1,
                    *   "gift_code": "G001",
                    *   "gift_name": "Xe cho bé",
                    *   "gift_image_url": "/images/xe.png",
                    *   "gift_image_mobile_url": "/images/xe.png",
                    *   "gift_points": 100,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00"
                *   }
            * ]
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function getHistoryExchangedGifts(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $filter = [

            ];
            $gifts = $this->userService->getHistoryExchangedGifts($perPage, $filter);
            $gifts->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($gifts);
            $pagingArr = $gifts->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'gifts' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Forgot my password
     * @group User management
     *
     * @bodyParam email email required . Example: mra@yopmail.com
     * @response {
        *   "status": true,
        *   "message": "Request success",
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function forgetPassword(Request $request){
        $this->validate($request, [
            'email'  => 'bail|required|email|exists:users,email'
        ]);
        try{
            $data = $request->all();
            $result = $this->userService->forgetPassword($data);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Reset my password
     * @group User management
     *
     * @bodyParam token string required . Example: 1234
     * @bodyParam email email required . Example: mra@yopmail.com
     * @bodyParam password string required . Example: 12345
     * @response {
        *   "status": true,
        *   "message": "Request success",
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function resetPassword(Request $request){
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|max:20|confirmed',
        ], [
            'token.required' => trans('validate.required', ['attribute' => 'Token']),
            'email.required' => trans('validate.required', ['attribute' => 'Email']),
            'email.email' => trans('validate.email', ['attribute' => 'Email']),
            'password.required' => trans('validate.required', ['attribute' => 'Mật khẩu']),
            'password.min' => trans('validate.min', ['attribute' => 'Mật khẩu', 'value' => 6]),
            'password.max' => trans('validate.max', ['attribute' => 'Mật khẩu', 'value' => 20]),
        ]);

        try{
            $status = Password::reset(
                $request->only('email', 'password', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ]);
                    $user->save();
                    event(new PasswordReset($user));
                }
            );
            \Log::info("status", [$status]);
            if($status === Password::PASSWORD_RESET){
                return $this->_success('Đổi mật khẩu thành công');
            }
            return $this->_error('Đổi mật khẩu thất bại');
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
