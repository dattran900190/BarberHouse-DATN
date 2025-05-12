<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceRequest;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $services = Service::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })->orderBy('id', 'DESC')->paginate(10);

        return view('admin.services.index', compact('services'));
    }

     public function create()
    {
        return view('admin.services.create');
    }

    public function store(ServiceRequest $request)
    {
        $data = $request->validated();
        
        // Nếu có ảnh thì lưu vào storage/app/public/services
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }


        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Thêm chi nhánh thành công');
    }

    public function show(Service $service_id)
    {
        return view('admin.services.show', compact('service_id'));
    }

     public function edit(Service $service_id)
    {
        return view('admin.services.edit', compact('service_id'));
    }

     public function update(ServiceRequest $request, Service $service_id)
    {

        $data = $request->validated();

        // Nếu có ảnh mới được upload
        if ($request->hasFile('image')) {
            // Xoá ảnh cũ nếu tồn tại
            if ($service_id->image && Storage::disk('public')->exists($service_id->image)) {
                Storage::disk('public')->delete($service_id->image);
            }

            // Lưu ảnh mới vào thư mục services
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service_id->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Service $service_id)
    {
         // Xóa ảnh nếu tồn tại
        if ($service_id->image && Storage::disk('public')->exists($service_id->image)) {
            Storage::disk('public')->delete($service_id->image);
        }

        $service_id->delete();
        return redirect()->route('admin.services.index')->with('success', 'Xoá chi nhánh thành công');
    }
}
