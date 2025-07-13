<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBranchAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Cho phép cả admin và admin_branch
        if (!$user || !in_array($user->role, ['admin', 'admin_branch'])) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
