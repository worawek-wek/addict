<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if ($request->is('admin/*')) {
            return route('admin.login');
        } elseif ($request->is('customer/*')) {
            return route('customer.login');
        }

        return route('customer.login'); // fallback
    }
}

