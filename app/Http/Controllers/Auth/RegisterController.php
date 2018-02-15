<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'is_active' => false,
            'activation_code' => md5($data['name']),
        ]);
        $user->relationUserRegistrationLog()->create(['user_id' => $user->id, 'registration_time' => Carbon::now()]);

        return $user;
    }

    /**
     * Регистрация пользователя
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));
        $this->sendActivationMail($request->all());

        return redirect()->back()->with('message','Регистрация прошла успешно, осталось подтвердить почту!');
    }

    /**
     * Отправка письма для подтверждения регистрации
     */
    public function sendActivationMail($array)
    {
        $data = User::all()->where('email', $array['email'])->first()->toArray();
        $data['url'] = URL::to('/');
        return Mail::send('auth.email', ['data' => $data],  function($message) use ($data){
            $mail_admin = env('MAIL_ADMIN');

            $message->from($mail_admin, 'Admin');
            $message->to($data['email'])->subject('Registration');
        });
    }

    /**
     * Активация пользователя
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function activeCode(Request $request)
    {
        $email = $request->email;
        $code = $request->code;

        $user = User::where('email', $email)->first();
        if($user->activation_code == $code) {
            $user->is_active = true;
            $user->save();
            return redirect('/login')->with(['message' => 'Почта подтверждена, можете войти на сайт']);
        }
        return false;
    }
}
