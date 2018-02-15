<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * авторизация пользователя
     */
    public function login(Request $request)
    {
        $array = $request->all();
        $remember = $request->has('remember');

        if(Auth::attempt([
            'email' => $array['email'],
            'password' => $array['password'],
            'is_active' => true,
        ], $remember)) {
            return redirect()->intended('/home');
        }
        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'login' => 'Данные аутентификации не верны'
            ]);
    }
}
