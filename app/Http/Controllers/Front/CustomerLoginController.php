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
            return redirect()->intended('/home');
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
        // ถ้าต้องการจำกัด contact_app ให้ตรงกับหน้า (line/whatsapp/wechat/email)
        $request->validate(
            [
                'first_name'          => 'required|string|max:255',
                'last_name'           => 'required|string|max:255',
                'nationality'         => 'nullable|string|max:100',
                'phone'               => 'required|digits:10|unique:customers,phone',
                'contact_app'         => 'nullable|in:line,whatsapp,wechat,email',
                'contact_app_handle'  => 'nullable|string|max:255',
                'password'            => 'required|string|min:6|confirmed',
            ],
            // messages (ไทย)
            [
                'required'  => ':attribute จำเป็นต้องกรอก',
                'string'    => ':attribute ต้องเป็นตัวอักษร',
                'max'       => ':attribute ต้องไม่เกิน :max ตัวอักษร',
                'unique'    => ':attribute นี้ถูกใช้ไปแล้ว',
                'in'        => ':attribute ไม่ถูกต้อง',
                'email'     => ':attribute รูปแบบไม่ถูกต้อง',
                'min'       => ':attribute ต้องมีอย่างน้อย :min ตัวอักษร',
                'confirmed' => 'ยืนยันรหัสผ่านไม่ตรงกัน',
                'digits'    => ':attribute ต้องเป็นตัวเลข :digits หลัก',
                'regex'     => ':attribute รูปแบบไม่ถูกต้อง',
            ],
            // attributes (label ไทย)
            [
                'first_name'         => 'ชื่อ',
                'last_name'          => 'นามสกุล',
                'nationality'        => 'สัญชาติ',
                'phone'              => 'เบอร์โทร',
                'contact_app'        => 'แอปติดต่อ',
                'contact_app_handle' => 'ไอดี/เบอร์ในแอป',
                'password'           => 'รหัสผ่าน',
            ]
        );


        $user = Customer::create([
            'name'               => (string) $request->first_name . ' ' . (string) $request->last_name,
            'first_name'         => (string) $request->first_name,
            'last_name'          => (string) $request->last_name,
            'nationality'        => $request->nationality ? (string) $request->nationality : null,
            'phone'              => (string) $request->phone,
            'contact_app'        => $request->contact_app ? (string) $request->contact_app : null,
            'contact_app_handle' => $request->contact_app_handle ? (string) $request->contact_app_handle : null,
            'password'           => Hash::make((string) $request->password),
            'ref_branch_id' => 1
        ]);

        if ($user) {
            auth()->login($user);
            return redirect()->route('dashboard')->with('success', 'สมัครสมาชิกเรียบร้อย');
        }

        return back()->with('error', 'ไม่สามารถสมัครสมาชิกได้ กรุณาลองอีกครั้ง');
    }
}
