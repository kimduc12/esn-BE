<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($message, $data = null, $status = 200, $headers = []) {
            $array = [
                'status' => true,
                'message' => $message
            ];
            if(!empty($data)) $array['data'] = $data;
            return Response::make($array, $status, $headers);
        });
        Response::macro('error', function ($message, $data = null, $status = 200, $headers = []) {
            $array = [
                'status' => false,
                'message' => $message
            ];
            if(!empty($data)) $array['data'] = $data;
            return Response::make($array, $status, $headers);
        });
        Response::macro('errors', function ($message, $errors = [], $status = 200, $headers = []) {
            $array = [
                'status' => false,
                'message' => $message,
                'errors' => $errors
            ];
            return Response::make($array, $status, $headers);
        });
        Response::macro('result', function ($data = null, $message = null, $status = 200, $headers = []) {
            $array = [
                'status' => true,
                'data' => $data
            ];
            if(!empty($message)) $array['message'] = $message;
            return Response::make($array, $status, $headers);
        });
    }
}
