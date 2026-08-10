<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\CafeTable;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('customer.menu');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            return $user->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('customer.menu');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Guest customer login (no account needed)
    public function guestSelect()
    {
        $tables = CafeTable::all();
        return view('auth.guest', compact('tables'));
    }

    public function guestLogin(Request $request)
    {
        $request->validate([
            'table_id'      => 'required|exists:tables,id',
            'customer_name' => 'nullable|string|max:100',
        ]);

        session([
            'guest_table_id'      => $request->table_id,
            'guest_customer_name' => $request->customer_name ?: 'Tamu',
        ]);

        return redirect()->route('customer.menu');
    }
}
