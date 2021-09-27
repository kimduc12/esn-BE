<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class RestfulController extends Controller
{
    /**
    *  Default HTTP statuses
    */
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN   = 403;
    const HTTP_NOT_ACCEPTABLE = 422;
    const HTTP_INTERNAL_ERROR = 500;

    /**
    *  Default error messages
    */
    const RESOURCE_UNAUTHORIZED = 'Unauthenicate.';
    const RESOURCE_NOT_FOUND = 'Resource not found.';
    const RESOURCE_METHOD_NOT_ALLOWED = 'Resource does not support method.';
    const RESOURCE_METHOD_NOT_IMPLEMENTED = 'Resource method not implemented yet.';
    const RESOURCE_INTERNAL_ERROR = 'Resource internal error.';
    const RESOURCE_DATA_PRE_VALIDATION_ERROR = 'Resource data pre-validation error.';
    const RESOURCE_DATA_INVALID = 'Resource data invalid.';
    const RESOURCE_UNKNOWN_ERROR = 'Resource unknown error.';
    const RESOURCE_REQUEST_DATA_INVALID = 'The request data is invalid.';
    const RESOURCE_PERMISSION_DENIED = 'The request is forbidden for this user/role';

    /**
    * Collection page sizes
    */
    const PAGE_SIZE_DEFAULT = 10;
    const PAGE_SIZE_MAX = 100;

    /**
    * Constructor
    */
    public function __construct ()
    {
        App::setLocale(request()->route('locale', 'en'));
    }

    /**
    * Validate request attributes
    * @param   \Illuminate\Http\Request $request
    * @param   array $rules
    * @param   array $messages
    * @param   array $customAttributes
    * @return  object
    */
    public function validate (\Illuminate\Http\Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);
        if ($validator->fails()) {
            ob_start('ob_gzhandler');
            http_response_code(self::HTTP_OK);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ]);
            die();
        }
    }

    /**
    * Print error message
    * @param  string|array $message
    * @param  integer $code
    * @return string
    */
    protected function _error ($message, $code = self::HTTP_OK)
    {
        if (is_array($message) || is_object($message)) {
            \Log::error("API ".\Route::current()->uri, [$message]);
            $message = self::RESOURCE_INTERNAL_ERROR;
        }
        return response()->error($message, null, $code);
    }

    /**
    * Print success message
    * @param  string|array $message
    * @param  integer $code
    * @return string
    */
    protected function _success ($message, $code = self::HTTP_OK)
    {
        return response()->success($message, null, $code);
    }

    /**
    * Print JSON response
    * @param  array $data
    * @param  string $message
    * @param  integer $code
    * @param  array $headers
    * @return string
    */
    protected function _response ($data = [], $message = null, $status = self::HTTP_OK, $headers = [])
    {
        return response()->result($data, $message, $status, $headers);
    }

    /**
     * Get paginator data
     * @param   array $paginate
     * @return  object
     */
    protected function getPaginator ($paginate)
    {
        $data = [];
        if (is_object($paginate) && !empty($paginate)) {
            $data = $paginate->toArray();
        }
        unset($data['data']);
        return $data;
    }

    protected function _responseVnPay ($code, $message, $e = null)
    {
        if ($e != null){
            \Log::error("API ".\Route::current()->uri, [$e]);
        }
        \Log::error("API ".\Route::current()->uri, [$code, $message]);
        return response()->json([
            'RspCode' => $code,
            'Message' => $message
        ]);
    }
}
