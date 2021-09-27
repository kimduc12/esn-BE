<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Http\Controllers\Api\RestfulController;
class ApiAuthenticate
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $check = $this->authenticate($request, $guards);
        if($check == "false"){
            return response(json_encode([
                'status'=> false,
                'message'=> RestfulController::RESOURCE_UNAUTHORIZED
            ]), RestfulController::HTTP_UNAUTHORIZED, ['Content-Type' => 'application/json']);
        }

        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        return "false";
    }

    protected function checkPermission($request)
    {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        // decode JWT
        $publicKey = file_get_contents(storage_path('oauth-public.key'));
        $data = JWT::decode($token, $publicKey, array('RS256'));
        $scopes = $data->scopes;
        if (empty($scopes)) {
            return false;
        }
        $resource = str_replace(['@', 'Controller'], [':', ''], class_basename($request->route()->getAction()['controller']));
        foreach ($scopes as $scope) {
            if ($scope == $resource) {
                return true;
            }
        }
        return false;
    }
}
