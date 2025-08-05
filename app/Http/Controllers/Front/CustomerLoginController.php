<?php
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('frontend.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('id_card', 'password');

        if (Auth::guard('customer')->attempt($credentials)) {
            return redirect()->intended('/home');
        }

        return back()->withErrors([
            'id_card' => 'Invalid credentials',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        // เคลียร์ session ทั้งหมด
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login'); // หรือเปลี่ยน path ได้ตามต้องการ
    }
}
