<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (in_array(Auth::user()->role, ['admin', 'admin_branch'])) {
                return $next($request);
            } else {
                return redirect()->route('login')->with([
                    'messageError' => 'Bạn không có quyền đăng nhập'
                ]);
            }
        }

        return redirect()->route('home')->with('messageError', 'Bạn không có quyền truy cập khu vực quản trị');
    }
}