<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\BarberRequest;

class BarberController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $barbers = Barber::with('branch')
            ->when($user->role === 'admin_branch', function ($query) use ($user) {
                return $query->where('branch_id', $user->branch_id);
            })
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderByDesc('id')
            ->paginate(5);

        return view('admin.barbers.index', compact('barbers', 'search'));
    }


    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('barbers.index')->with('error', 'Bạn không có quyền thêm thợ cắt tóc.');
        }
        $branches = Branch::all(); // Lấy danh sách chi nhánh
        return view('admin.barbers.create', compact('branches'));
    }

    public function store(BarberRequest $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('barbers.index')->with('error', 'Bạn không có quyền thêm thợ cắt tóc.');
        }
        $data = $request->validated();
        // dd($data);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $data['status'] = 'idle';
        Barber::create($data);

        return redirect()->route('barbers.index')->with('success', 'Thêm thợ thành công');
    }

    public function show(Barber $barber)
    {
        $barber->load('branch'); // Nạp chi nhánh
        return view('admin.barbers.show', compact('barber'));
    }


    public function edit(Barber $barber)
    {
        $branches = Branch::all(); // Lấy danh sách chi nhánh
        return view('admin.barbers.edit', compact('barber', 'branches'));
    }

    public function update(BarberRequest $request, Barber $barber)
    {
        $data = $request->validated();

        $currentStatus = $barber->status;
        $newStatus = $data['status'] ?? $currentStatus;

        if ($currentStatus === 'retired' && $newStatus !== 'retired') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['status' => 'Không thể thay đổi trạng thái khi thợ đã nghỉ việc.']);
        }

        // Xử lý ảnh đại diện
        if ($request->hasFile('avatar')) {
            if ($barber->avatar && Storage::disk('public')->exists($barber->avatar)) {
                Storage::disk('public')->delete($barber->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            $data['avatar'] = $barber->avatar;
        }

        $barber->update($data);

        return redirect()->route('barbers.index', ['page' => $request->input('page', 1)])
            ->with('success', 'Cập nhật thành công');
    }



    public function destroy(Barber $barber)
    {
        // Lấy lại số trang hiện tại từ form
        $page = request('page', 1);

        // Kiểm tra lịch hẹn còn hoạt động
        $hasActiveAppointments = $barber->appointments()
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->exists();

        if ($hasActiveAppointments) {
            return redirect()->route('barbers.index', ['page' => $page])
                ->with('error', 'Không thể vô hiệu hóa thợ vì còn lịch hẹn chưa hoàn tất.');
        }

        $barber->status = 'retired';
        $barber->save();

        return redirect()->route('barbers.index', ['page' => $page])
            ->with('success', 'Thợ đã nghỉ việc.');
    }
}
