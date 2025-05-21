<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\BarberRequest;


class BarberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $barbers = Barber::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })->latest()->paginate(10);

        return view('admin.barbers.index', compact('barbers', 'search'));
    }


    public function create()
    {
        return view('admin.barbers.create');
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
        return view('admin.barbers.show', compact('barber'));
    }

    public function edit(Barber $barber)
    {
        return view('admin.barbers.edit', compact('barber'));
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
        if ($barber->avatar && Storage::disk('public')->exists($barber->avatar)) {
            Storage::disk('public')->delete($barber->avatar);
        }

        $barber->delete();

        return redirect()->route('barbers.index')->with('success', 'Xóa thợ thành công');
    }
}
