<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('frontend.login');
    }
    public function showRegisterForm()
    {
        return view('frontend.register');
    }
    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        // หา customer ด้วย phone แทน id_card
        $customer = Customer::where('phone', $credentials['phone'])->first();

        if ($customer && Hash::check($credentials['password'], $customer->password)) {
            if ($customer->status === 0) {
                return back()->withErrors([
                    'phone' => 'Please contact staff.',
                ]);
            }

            Auth::guard('customer')->login($customer);
            // Redirect ไปหน้า customer dashboard หลัง login
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'phone' => 'Invalid credentials.',
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
    public function register(Request $request)
    {
        // Set locale based on request (default to Thai)
        $locale = $request->input('locale', 'th');
        app()->setLocale($locale);

        $request->validate([
            'first_name'          => 'required|string|max:255',
            'last_name'           => 'required|string|max:255',
            'nationality'         => 'nullable|string|max:100',
            'phone'               => 'required|digits:10|unique:customers,phone',
            'contact_line'        => 'nullable|string|max:255',
            'contact_whatsapp'    => 'nullable|string|max:255',
            'contact_wechat'      => 'nullable|string|max:255',
            'contact_telegram'    => 'nullable|string|max:255',
            'contact_email'       => 'nullable|email|max:255',
            'password'            => 'required|string|min:6|confirmed',
        ]);

        if (empty($request->contact_line) && empty($request->contact_whatsapp) &&
            empty($request->contact_wechat) && empty($request->contact_telegram) &&
            empty($request->contact_email)) {
            $errorMsg = $locale === 'en'
                ? 'Please provide at least one contact method'
                : 'กรุณากรอกช่องทางติดต่ออย่างน้อย 1 ช่อง';
            return back()->withInput()->with('error', $errorMsg);
        }

        $user = Customer::create([
            'name'               => (string) $request->first_name . ' ' . (string) $request->last_name,
            'first_name'         => (string) $request->first_name,
            'last_name'          => (string) $request->last_name,
            'nationality'        => $request->nationality ? (string) $request->nationality : null,
            'phone'              => (string) $request->phone,
            'contact_line'       => $request->contact_line ? (string) $request->contact_line : null,
            'contact_whatsapp'   => $request->contact_whatsapp ? (string) $request->contact_whatsapp : null,
            'contact_wechat'     => $request->contact_wechat ? (string) $request->contact_wechat : null,
            'contact_telegram'   => $request->contact_telegram ? (string) $request->contact_telegram : null,
            'contact_email'      => $request->contact_email ? (string) $request->contact_email : null,
            'password'           => Hash::make((string) $request->password),
            'ref_branch_id' => 1
        ]);

        if ($user) {
            auth()->login($user);
            $successMsg = $locale === 'en' ? 'Registration successful' : 'สมัครสมาชิกเรียบร้อย';
            return redirect()->route('dashboard')->with('success', $successMsg);
        }

        $errorMsg = $locale === 'en' ? 'Unable to register. Please try again.' : 'ไม่สามารถสมัครสมาชิกได้ กรุณาลองอีกครั้ง';
        return back()->with('error', $errorMsg);
    }
}
