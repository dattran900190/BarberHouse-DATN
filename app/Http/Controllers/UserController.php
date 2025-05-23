<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role', 'user'); // Giữ role mặc định để xác định tab active

        // Lấy danh sách người dùng
        $users = User::where('role', 'user')
            ->when($search && $request->input('role_filter') === 'user', function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%');
            })->orderBy('id', 'DESC')->paginate(10, ['*'], 'users_page');

        // Lấy danh sách quản trị viên
        $admins = User::whereIn('role', ['admin', 'staff', 'editor'])
            ->when($search && $request->input('role_filter') === 'admin', function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%');
            })->orderBy('id', 'DESC')->paginate(10, ['*'], 'admins_page');

        return view('admin.users.index', compact('users', 'admins', 'role', 'search'));
    }

    // Các phương thức khác giữ nguyên như trong Cách 2 trước đó
    public function create(Request $request)
    {
        $role = $request->input('role', 'user');
        return view('admin.users.create', compact('role'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $role = $request->input('role', 'user');

        if ($role === 'admin' && !in_array($data['role'], ['admin', 'staff', 'editor'])) {
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

        return redirect()->route('users.index', ['role' => $role])
            ->with('success', 'Thêm ' . ($role === 'user' ? 'người dùng' : 'quản trị viên') . ' thành công');
    }

    public function show(User $user, Request $request)
    {
        $role = $request->input('role', 'user');
        if (($role === 'user' && $user->role !== 'user') || 
            ($role === 'admin' && !in_array($user->role, ['admin', 'staff', 'editor']))) {
            abort(403, 'Không có quyền truy cập');
        }
        return view('admin.users.show', compact('user', 'role'));
    }

    public function edit(User $user, Request $request)
    {
        $role = $request->input('role', 'user');
        if (($role === 'user' && $user->role !== 'user') || 
            ($role === 'admin' && !in_array($user->role, ['admin', 'staff', 'editor']))) {
            abort(403, 'Không có quyền truy cập');
        }
        return view('admin.users.edit', compact('user', 'role'));
    }

    public function update(UserRequest $request, User $user)
    {
        $role = $request->input('role', 'user');
        if (($role === 'user' && $user->role !== 'user') || 
            ($role === 'admin' && !in_array($user->role, ['admin', 'staff', 'editor']))) {
            abort(403, 'Không có quyền truy cập');
        }

        $data = $request->validated();

        if ($role === 'admin' && !in_array($data['role'], ['admin', 'staff', 'editor'])) {
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

        return redirect()->route('users.index', ['role' => $role])
            ->with('success', 'Cập nhật ' . ($role === 'user' ? 'người dùng' : 'quản trị viên') . ' thành công');
    }

    public function destroy(User $user, Request $request)
    {
        $role = $request->input('role', 'user');
        if (($role === 'user' && $user->role !== 'user') || 
            ($role === 'admin' && !in_array($user->role, ['admin', 'staff', 'editor']))) {
            abort(403, 'Không có quyền truy cập');
        }

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
        return redirect()->route('users.index', ['role' => $role])
            ->with('success', 'Xoá ' . ($role === 'user' ? 'người dùng' : 'quản trị viên') . ' thành công');
    }
}