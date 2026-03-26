<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // ログイン成功後に呼ばれる（ログイン直後だけモーダルを出すフラグを立てる）
    protected function authenticated(Request $request, $user)
    {
        $request->session()->put('open_home_modal', true);
    }

    // ログイン時のバリデーションを上書き
    protected function validateLogin(Request $request)
    {
        $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ],
            [
                'email.required' => 'メールアドレスは必須項目です。',
                'email.email' => 'メールアドレスの形式で入力してください。',
                'password.required' => 'パスワードは必須項目です。',
            ]
        );
    }
}