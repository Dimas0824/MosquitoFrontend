<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email atau kata sandi admin tidak valid.']);
        }

        session([
            'admin_id' => $user->id,
            'admin_email' => $user->email,
            'is_admin' => true,
        ]);

        Log::info('Admin logged in', [
            'admin_id' => $user->id,
            'admin_email' => $user->email,
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_id', 'admin_email', 'is_admin']);

        Log::info('Admin logged out');

        return redirect()->route('admin.login')->with('success', 'Anda telah keluar sebagai admin.');
    }
}
