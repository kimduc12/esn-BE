<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/', [WebController::class, 'index']);
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('/password/reset/{token}', [WebController::class, 'resetPassword'])->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
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

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ]);

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
                ? redirect(env('APP_FE_URL'))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');
