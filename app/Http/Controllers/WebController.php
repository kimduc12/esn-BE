<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{

    public function __construct(){

    }

    public function index(){
        return view('home');
    }

    public function resetPassword(Request $request, $token){
        $email = $request->get('email');
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $email,
        ]);
    }
}
