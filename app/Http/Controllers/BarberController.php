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
        $filter = $request->input('filter', 'all');

        $query = match ($filter) {
            'deleted' => Barber::onlyTrashed(),
            'active' => Barber::query(),
            default => Barber::withTrashed(),
        };

        $barbers = $query->with('branch')
            ->when($user->role === 'admin_branch', fn($q) => $q->where('branch_id', $user->branch_id))
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderByDesc('id')
            ->paginate(5);

        return view('admin.barbers.index', compact('barbers', 'search', 'filter'));
    }

    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('barbers.index')->with('error', 'Bạn không có quyền thêm thợ cắt tóc.');
        }
        $branches = Branch::all();
        return view('admin.barbers.create', compact('branches'));
    }

    public function store(BarberRequest $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('barbers.index')->with('error', 'Bạn không có quyền thêm thợ cắt tóc.');
        }

        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $data['status'] = 'idle';
        Barber::create($data);

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

        return redirect()->route('barbers.index', ['page' => $request->input('page', 1)])
            ->with('success', 'Cập nhật thành công');
    }

    public function destroy(Barber $barber)
    {
        $page = request('page', 1);

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

    public function softDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xoá thợ.'
            ]);
        }

        $barber = Barber::findOrFail($id);

        if ($barber->status !== 'retired') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ thợ đã nghỉ việc mới được xoá mềm.'
            ]);
        }

        $barber->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá mềm thợ.'
        ]);
    }


    public function restore($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền khôi phục thợ.'
            ]);
        }

        $barber = Barber::withTrashed()->findOrFail($id);
        $barber->restore();

        return response()->json([
            'success' => true,
            'message' => 'Khôi phục thợ thành công.'
        ]);
    }
}
