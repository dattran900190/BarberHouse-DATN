<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCustomerAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Nếu không đăng nhập, cho phép truy cập (guest users)
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Kiểm tra nếu user có role là admin hoặc admin_branch
        if (in_array($user->role, ['admin', 'admin_branch'])) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện hành động này'
                ], 403);
            }
            
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này');
        }

        // Cho phép user thông thường truy cập
        return $next($request);
    }
}

