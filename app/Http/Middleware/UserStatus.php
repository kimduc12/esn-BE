<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Http\Controllers\Api\RestfulController;

class UserStatus
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
     * @return mixed
     */
    public function handle($request, Closure $next, $field = null)
    {
        if($this->auth->user())
        {
            if($this->auth->user()->status != $field) {
                return response(json_encode([
                    'status'=> false,
                    'message'=> RestfulController::RESOURCE_UNAUTHORIZED
                ]), RestfulController::HTTP_UNAUTHORIZED, ['Content-Type' => 'application/json']);
            }
        }
        return $next($request);
    }
}
