<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserRegisterController extends Controller
{
    public function index()
    {
        return view('user.user_register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 0,
            'mail_flg' => $request->has('mail_flg') ? 1 : 0,
        ]);

        return redirect()->route('login')->with('success', '登録が完了しました');
    }

    public function list()
    {
        $users = User::all();
        return view('user.user_list', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('user.user_edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|confirmed|min:6',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // ID1は権限変更不可
        if ($user->id != 1) {
            $user->role = $request->role;
        }

        $user->mail_flg = $request->has('mail_flg') ? 1 : 0;

        // パスワード変更（入力されている時だけ）
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user_list')
            ->with('success', 'ユーザー情報を更新しました');
    }

    public function destroy($id)
    {
        if ($id == 1) {
            return redirect()->route('user_list')
                ->with('error', 'ユーザーID1は削除できません');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user_list')
            ->with('success', '削除しました');
    }
}