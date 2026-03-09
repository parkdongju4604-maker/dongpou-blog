<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    private const ADMIN_ID       = 'admin';
    private const ADMIN_PASSWORD = 'xoals2147';

    public function showLogin()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.posts.index');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (
            $request->username === self::ADMIN_ID &&
            $request->password === self::ADMIN_PASSWORD
        ) {
            $request->session()->put('admin_logged_in', true);
            return redirect()->route('admin.posts.index');
        }

        return back()->withErrors(['login' => '아이디 또는 비밀번호가 올바르지 않습니다.'])->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_logged_in');
        return redirect()->route('admin.login');
    }
}
