<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\BarberRequest;

class BarberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $barbers = Barber::with('branch') // load thông tin chi nhánh
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(5);

        return view('admin.barbers.index', compact('barbers', 'search'));
    }

    public function create()
    {
        $branches = Branch::all(); // Lấy danh sách chi nhánh
        return view('admin.barbers.create', compact('branches'));
    }

    public function store(BarberRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

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

        if ($request->hasFile('avatar')) {
            if ($barber->avatar && Storage::disk('public')->exists($barber->avatar)) {
                Storage::disk('public')->delete($barber->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            $data['avatar'] = $barber->avatar;
        }

        $barber->update($data);

        return redirect()->route('barbers.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Barber $barber)
    {
        // Kiểm tra lịch hẹn có trạng thái không phải 'cancelled' hoặc 'completed'
        $hasActiveAppointments = $barber->appointments()
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->exists();

        if ($hasActiveAppointments) {
            return redirect()->route('barbers.index')
                ->with('error', 'Không thể xóa thợ vì còn lịch hẹn chưa hoàn tất.');
        }


        // Kiểm tra nếu còn đánh giá liên quan
        if ($barber->reviews()->exists()) {
            return redirect()->route('barbers.index')
                ->with('error', 'Không thể xóa thợ vì còn đánh giá liên quan.');
        }

        // Xóa avatar nếu có
        if ($barber->avatar && Storage::disk('public')->exists($barber->avatar)) {
            Storage::disk('public')->delete($barber->avatar);
        }

        // Xóa thợ
        $barber->delete();

        return redirect()->route('barbers.index')->with('success', 'Xóa thợ thành công');
    }
}
