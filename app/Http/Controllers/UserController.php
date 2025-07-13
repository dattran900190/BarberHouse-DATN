<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role', 'user');

        $users = User::where('role', 'user')
            ->when($search && $request->input('role_filter') === 'user', function ($query) use ($search) {
                return $query->where(function ($query2) use ($search) {
                    $query2->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })->orderBy('id', 'DESC')->paginate(10);

        $admins = User::whereIn('role', ['admin', 'admin_branch', 'super_admin'])
            ->when($search && $request->input('role_filter') === 'admin', function ($query) use ($search) {
                return $query->where(function ($query2) use ($search) {
                    $query2->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })->orderBy('id', 'DESC')->paginate(10);

        return view('admin.users.index', compact('users', 'admins', 'role', 'search'));
    }



    public function create(Request $request)
    {

        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền thêm người dùng.');
        }
        $role = $request->input('role', 'user');
        $status = $request->input('status', 'active');
        return view('admin.users.create', compact('role'));
    }

    public function store(UserRequest $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền thêm người dùng.');
        }
        $data = $request->validated();
        $role = $request->input('role', 'user');
        $data['status'] = $request->input('status', 'active'); // set mặc định là active

        if ($role === 'admin' && !in_array($data['role'], ['admin', 'admin_branch', 'super_admin'])) {
            return back()->withErrors(['role' => 'Vai trò không hợp lệ cho quản trị viên']);
        }
        if ($role === 'user' && $data['role'] !== 'user') {
            return back()->withErrors(['role' => 'Vai trò không hợp lệ cho người dùng']);
        }

        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('users.index', [
            'role' => $role
        ])->with([
            'success' => 'Thêm ' . ($role === 'user' ? 'người dùng' : 'quản trị viên') . ' thành công',
            'status' => $data['status']
        ]);
    }


    public function show(User $user, Request $request)
    {
        $role = $request->input('role', 'user');
        if (($role === 'user' && $user->role !== 'user') ||
            ($role === 'admin' && !in_array($user->role, ['admin', 'admin_branch', 'super_admin']))
        ) {
            abort(403, 'Không có quyền truy cập');
        }
        return view('admin.users.show', compact('user', 'role'));
    }

    public function edit(User $user, Request $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền sửa người dùng.');
        }
        $role = $request->input('role', 'user');
        if (($role === 'user' && $user->role !== 'user') ||
            ($role === 'admin' && !in_array($user->role, ['admin', 'admin_branch', 'super_admin']))
        ) {
            abort(403, 'Không có quyền truy cập');
        }
        return view('admin.users.edit', compact('user', 'role'));
    }

    public function update(UserRequest $request, User $user)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền sửa người dùng.');
        }
        $currentPage = $request->input('page', 1);
        $role = $request->input('role', 'user');
        if (($role === 'user' && $user->role !== 'user') ||
            ($role === 'admin' && !in_array($user->role, ['admin', 'admin_branch', 'super_admin']))
        ) {
            abort(403, 'Không có quyền truy cập');
        }

        $data = $request->validated();

        if ($role === 'admin' && !in_array($data['role'], ['admin', 'admin_branch', 'super_admin'])) {
            return back()->withErrors(['role' => 'Vai trò không hợp lệ cho quản trị viên']);
        }
        if ($role === 'user' && $data['role'] !== 'user') {
            return back()->withErrors(['role' => 'Vai trò không hợp lệ cho người dùng']);
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('users.index', [
            'role' => $role,
            'page' => $currentPage
        ])->with('success', 'Cập nhật ' . ($role === 'user' ? 'người dùng' : 'quản trị viên') . ' thành công');
    }

    public function destroy(User $user, Request $request)
    {   
        
        $role = $request->input('role', 'user');
        if (($role === 'user' && $user->role !== 'user') ||
            ($role === 'admin' && !in_array($user->role, ['admin', 'admin_branch', 'super_admin']))
        ) {
            abort(403, 'Không có quyền truy cập');
        }

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
        return redirect()->route('users.index', ['role' => $role])
            ->with('success', 'Xoá ' . ($role === 'user' ? 'người dùng' : 'quản trị viên') . ' thành công');
    }
    public function toggleStatus(Request $request, User $user)
    {
        
        $user->status = $user->status === 'active' ? 'banned' : 'active';
        $user->save();

        return response()->json([
            'status' => $user->status,
            'button_label' => $user->status === 'active' ? 'Chặn' : 'Bỏ chặn',
            'badge_class' => $user->status === 'active' ? 'badge-success' : 'badge-danger',
        ]);
    }
}
