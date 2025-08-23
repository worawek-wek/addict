<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
protected function redirectTo($request)
{
    if ($request->is('admin') || $request->is('admin/*') || $request->is('pos') || $request->is('pos/*')) {
        return route('admin.login'); // หรือ pos.login ถ้ามี
    } elseif ($request->is('customer') || $request->is('customer/*')) {
        return route('customer.login');
    }

    return route('customer.login'); // fallback
}

}
