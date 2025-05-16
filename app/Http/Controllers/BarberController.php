<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarberController extends Controller
{
    public function index()
    {
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
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
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
        if ($barber->avatar && Storage::disk('public')->exists($barber->avatar)) {
            Storage::disk('public')->delete($barber->avatar);
        }

        $barber->delete();

        return redirect()->route('barbers.index')->with('success', 'Xóa thợ thành công');
    }
}
