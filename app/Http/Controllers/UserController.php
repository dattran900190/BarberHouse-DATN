<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role', 'user');
        $filter = $request->input('filter', 'all');

        $usersQuery = User::withTrashed()->where('role', 'user')
            ->when($search && $request->input('role_filter') === 'user', function ($query) use ($search) {
                return $query->where(function ($query2) use ($search) {
                    $query2->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            });

        $adminsQuery = User::withTrashed()->whereIn('role', ['admin', 'admin_branch'])
            ->when($search && $request->input('role_filter') === 'admin', function ($query) use ($search) {
                return $query->where(function ($query2) use ($search) {
                    $query2->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            });

        // Áp dụng bộ lọc trạng thái
        if ($filter === 'active') {
            $usersQuery->where('status', 'active')->whereNull('deleted_at');
            $adminsQuery->where('status', 'active')->whereNull('deleted_at');
        } elseif ($filter === 'banned') {
            $usersQuery->where('status', 'banned');
            $adminsQuery->where('status', 'banned');
        }

        $users = $usersQuery->orderBy('id', 'DESC')->paginate(10);
        $admins = $adminsQuery->orderBy('id', 'DESC')->paginate(10);

        return view('admin.users.index', compact('users', 'admins', 'role', 'search', 'filter'));
    }



    public function create(Request $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền thêm người dùng.');
        }
        $branches = Branch::whereNotIn('id', function ($query) {
            $query->select('branch_id')
                ->from('users')
                ->whereNotNull('branch_id')
                ->where('role', 'admin_branch');
        })->get();
        $role = $request->input('role', 'user');
        $status = $request->input('status', 'active');
        return view('admin.users.create', compact('role', 'branches'));
    }

    public function store(UserRequest $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền thêm người dùng.');
        }
        $data = $request->validated();
        $role = $request->query('role', 'user');

        $data['status'] = $request->input('status', 'active'); // set mặc định là active

        if ($role === 'admin' && !in_array($data['role'], ['admin', 'admin_branch'])) {
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
            ($role === 'admin' && !in_array($user->role, ['admin', 'admin_branch']))
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
            ($role === 'admin' && !in_array($user->role, ['admin', 'admin_branch']))
        ) {
            abort(403, 'Không có quyền truy cập');
        }

        // Kiểm tra xem người dùng đang chỉnh sửa có phải là chính họ hay không
        $isEditingSelf = Auth::user()->id === $user->id;

        // Lấy chi nhánh chưa gán cho admin_branch, nhưng giữ lại chi nhánh của user đang edit
        $branches = Branch::where(function ($query) use ($user) {
            $query->whereNotIn('id', function ($subQuery) {
                $subQuery->select('branch_id')
                    ->from('users')
                    ->whereNotNull('branch_id')
                    ->where('role', 'admin_branch');
            })
                ->orWhere('id', $user->branch_id); // giữ lại chi nhánh đã chọn nếu đang edit
        })->get();

        return view('admin.users.edit', compact('user', 'role', 'branches', 'isEditingSelf'));
    }


    public function update(UserRequest $request, User $user)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('users.index')->with('error', 'Bạn không có quyền sửa người dùng.');
        }

        $currentPage = $request->input('page', 1);
        $role = $request->query('role', 'user');
        $isEditingSelf = Auth::user()->id === $user->id;

        // Kiểm tra quyền truy cập theo vai trò
        if (($role === 'user' && $user->role !== 'user') ||
            ($role === 'admin' && !in_array($user->role, ['admin', 'admin_branch']))
        ) {
            abort(403, 'Không có quyền truy cập');
        }

        // Lấy dữ liệu hợp lệ từ request
        $data = $request->validated();

        // Nếu không phải đang chỉnh sửa chính mình → chỉ cho phép cập nhật role và status
        if (!$isEditingSelf) {
            $data = array_intersect_key($data, array_flip(['status', 'role']));
            // Gán lại gender thủ công nếu bị disabled trong form
            if ($request->has('gender_hidden')) {
                $data['gender'] = $request->input('gender_hidden');
            }
        }

        // Kiểm tra role hợp lệ theo ngữ cảnh
        if ($role === 'admin' && (!isset($data['role']) || !in_array($data['role'], ['admin', 'admin_branch']))) {
            return back()->withErrors(['role' => 'Vai trò không hợp lệ cho quản trị viên']);
        }

        if ($role === 'user' && (isset($data['role']) && $data['role'] !== 'user')) {
            return back()->withErrors(['role' => 'Vai trò không hợp lệ cho người dùng']);
        }

        // Xử lý mật khẩu nếu có và đang chỉnh sửa chính mình
        if ($request->filled('password') && $isEditingSelf) {
            $data['password'] = Hash::make($request->input('password'));
        } else {
            unset($data['password']);
        }

        // Xử lý ảnh đại diện nếu có
        if ($request->hasFile('avatar') && $isEditingSelf) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Cập nhật người dùng
        $previousStatus = $user->status;
        $user->update($data);
        if ($previousStatus === 'active' && isset($data['status']) && $data['status'] === 'banned') {
            $user->delete(); // xóa mềm
        }


        return redirect()->route('users.index', [
            'role' => $role,
            'page' => $currentPage
        ])->with('success', 'Cập nhật ' . ($role === 'user' ? 'người dùng' : 'quản trị viên') . ' thành công');
    }


    public function destroy($id, Request $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa người dùng.'
            ], 403);
        }

        $role = $request->input('role', 'user');

        try {
            $user = User::withTrashed()->findOrFail($id);

            // Xóa avatar nếu có
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Xoá vĩnh viễn
            $user->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ' . ($role === 'user' ? 'người dùng' : 'quản trị viên') . ' vĩnh viễn.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Trường hợp có khóa ngoại → không xoá được
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa vì người dùng đang liên kết với dữ liệu khác (ví dụ: lịch hẹn, đơn hàng, v.v).'
            ]); // Conflict
        } catch (\Exception $e) {
            // Lỗi không xác định
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa người dùng. Vui lòng thử lại sau.'
            ], 500);
        }
    }


    // xóa mềm
    public function softDelete(User $user, Request $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa người dùng.'
            ], 403);
        }

        $role = $request->input('role', 'user');

        try {
            // Cập nhật trạng thái thành banned
            $user->status = 'banned';
            $user->save(); // Lưu trạng thái trước khi xóa mềm

            // Xóa avatar nếu tồn tại
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
                $user->avatar = null;
                $user->save();
            }

            // Thực hiện xóa mềm
            $user->delete();

            // Kiểm tra xem xóa mềm có thành công không
            if ($user->trashed()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa ' . ($role === 'user' ? 'người dùng' : 'quản trị viên') . ' thành công'
                ], 200);
            } else {
                throw new \Exception('Xóa mềm không thành công');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa người dùng: ' . $e->getMessage()
            ], 500);
        }
    }
    public function restore($id, Request $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền khôi phục người dùng.'
            ], 403);
        }

        $role = $request->input('role', 'user');

        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->status = 'active';
            $user->save();
            $user->restore();

            Log::info('Khôi phục người dùng', ['user_id' => $user->id, 'status' => $user->status, 'deleted_at' => $user->deleted_at]);

            return response()->json([
                'success' => true,
                'message' => 'Khôi phục tài khoản thành công.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi khôi phục', ['user_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi khôi phục: ' . $e->getMessage()
            ], 500);
        }
    }
}
