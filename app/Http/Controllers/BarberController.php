<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function index()
    {
        // Sắp xếp các thợ cắt tóc theo ngày tạo (mới nhất lên đầu)
        $barbers = Barber::orderBy('created_at', 'desc')->get();
        return view('admin.barbers.index', compact('barbers'));
    }


    public function create()
    {
        return view('admin.barbers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'skill_level' => 'required|max:50',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'rating_avg' => 'nullable|numeric|min:0|max:5',
        ]);

        $data = $request->all();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['avatar'] = 'uploads/avatars/' . $filename;
        }

        Barber::create($data);

        return redirect()->route('barbers.index')->with('success', 'Thêm thợ thành công');
    }


    public function show(Barber $barber)
    {
        return view('admin.barbers.show', compact('barber'));
    }

    public function edit(Barber $barber)
    {
        return view('admin.barbers.edit', compact('barber'));
    }

    public function update(Request $request, Barber $barber)
    {
        $request->validate([
            'name' => 'required|max:100',
            'skill_level' => 'required|max:50',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'rating_avg' => 'nullable|numeric|min:0|max:5',
        ]);

        $data = $request->all();

        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($barber->avatar && file_exists(public_path($barber->avatar))) {
                unlink(public_path($barber->avatar));
            }

            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['avatar'] = 'uploads/avatars/' . $filename;
        } else {
            // Không cập nhật avatar mới => giữ nguyên ảnh cũ
            $data['avatar'] = $barber->avatar;
        }

        $barber->update($data);

        return redirect()->route('barbers.index')->with('success', 'Cập nhật thành công');
    }


    public function destroy(Barber $barber)
    {
        // Kiểm tra và xóa ảnh đại diện nếu có
        if ($barber->avatar && file_exists(public_path($barber->avatar))) {
            unlink(public_path($barber->avatar));  // Xóa file ảnh đại diện
        }

        // Xóa bản ghi thợ cắt tóc trong cơ sở dữ liệu
        $barber->delete();

        return redirect()->route('barbers.index')->with('success', 'Xóa thợ thành công');
    }
}
