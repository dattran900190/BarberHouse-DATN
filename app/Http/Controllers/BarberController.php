<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\BarberRequest;
use App\Events\BarberUpdated;

class BarberController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $filter = $request->input('filter', 'all');
        $today = now()->toDateString();

        $query = match ($filter) {
            'deleted' => Barber::onlyTrashed(),
            'active' => Barber::query(),
            default => Barber::withTrashed(),
        };

        $barbers = $query
            ->with([
                'branch',
                'schedules' => function ($query) use ($today) {
                    $query->whereDate('schedule_date', $today);
                }
            ])
            ->when($user->role === 'admin_branch', fn($q) => $q->where('branch_id', $user->branch_id))
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.barbers.index', compact('barbers', 'search', 'filter'));
    }

    public function create()
    {
        $user = Auth::user();

        // Nếu là admin_branch thì chỉ lấy ra chi nhánh của họ
        if ($user->role === 'admin_branch') {
            $branches = Branch::where('id', $user->branch_id)->get(); // giả sử có `branch_id` trong bảng users
        } else {
            $branches = Branch::all(); // admin thì lấy hết
        }

        return view('admin.barbers.create', compact('branches'));
    }

    public function store(BarberRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $data['status'] = 'idle';
        Barber::create($data);
        event(new BarberUpdated());

        return redirect()->route('barbers.index')->with('success', 'Thêm thợ thành công');
    }

    public function show($id)
    {

        $barber = Barber::withTrashed()->findOrFail($id);
        $barber->load('branch');
        return view('admin.barbers.show', compact('barber'));
    }

    public function edit(Barber $barber)
    {
        $branches = Branch::all();
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

        if ($request->hasFile('avatar')) {
            if ($barber->avatar && Storage::disk('public')->exists($barber->avatar)) {
                Storage::disk('public')->delete($barber->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            $data['avatar'] = $barber->avatar;
        }

        $barber->update($data);
        event(new BarberUpdated());

        return redirect()->route('barbers.index', ['page' => $request->input('page', 1)])
            ->with('success', 'Cập nhật thành công');
    }

    public function destroy(Barber $barber)
    {
        $hasActiveAppointments = $barber->appointments()
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->exists();

        if ($hasActiveAppointments) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể vô hiệu hóa thợ vì còn lịch hẹn chưa hoàn tất.'
            ]);
        }

        $barber->status = 'retired';
        $barber->save();
        event(new BarberUpdated());

        return response()->json([
            'success' => true,
            'message' => 'Thợ đã được cho nghỉ việc.'
        ]);
    }


    public function softDelete($id)
    {
        // if (Auth::user()->role === 'admin_branch') {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Bạn không có quyền xoá thợ.'
        //     ]);
        // }

        $barber = Barber::findOrFail($id);

        if ($barber->status !== 'retired') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ thợ đã nghỉ việc mới được xoá mềm.'
            ]);
        }

        $barber->delete();
        event(new BarberUpdated());

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá mềm thợ.'
        ]);
    }


    public function restore($id)
    {
        // if (Auth::user()->role === 'admin_branch') {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Bạn không có quyền khôi phục thợ.'
        //     ]);
        // }

        $barber = Barber::withTrashed()->findOrFail($id);
        $barber->restore();
        event(new BarberUpdated());

        return response()->json([
            'success' => true,
            'message' => 'Khôi phục thợ thành công.'
        ]);
    }
}
