<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
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
     * Add active check to credentials so deactivated users can't login
     */
    protected function credentials(Request $request)
    {
        return array_merge(
            $request->only($this->username(), 'password'),
            ['active' => 1]
        );
    }

    /**
     * Override authenticated callback to track last login
     */
    protected function authenticated(Request $request, $user)
    {
        $user->last_login = now();
        $user->save();
    }

    /**
     * Override failed login response to give clear message when account inactive.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && !$user->active) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                $this->username() => [trans('auth.inactive')],
            ]);
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}
