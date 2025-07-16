<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\CancelledAppointment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ServiceRequest;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'all'); // Mặc định là 'all' nếu không có

        // Lấy query builder tùy theo filter
        $query = match ($filter) {
            'active' => Service::query(), // chỉ service còn hoạt động
            'deleted' => Service::onlyTrashed(),
            default => Service::withTrashed(), // tất cả (kể cả đã xoá mềm)
        };

        // Thêm điều kiện search nếu có
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Sắp xếp & phân trang
        $services = $query->orderBy('updated_at', 'DESC')->paginate(5);

        return view('admin.services.index', compact('services', 'filter', 'search'));
    }


    public function create()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('services.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        return view('admin.services.create');
    }

    public function store(ServiceRequest $request)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('services.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $data = $request->validated();

        // Nếu có ảnh thì lưu vào storage/app/public/services
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }


        Service::create($data);

        return redirect()->route('services.index')->with('success', 'Thêm dịch vụ thành công');
    }

    public function show(Service $service)
    {
        return view('admin.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('services.index')->with('error', 'Bạn không có quyền chỉnh sửa.');
        }
        return view('admin.services.edit', compact('service'));
    }

    public function update(ServiceRequest $request, Service $service)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('services.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $data = $request->validated();

        // Nếu có ảnh mới được upload
        if ($request->hasFile('image')) {
            // Xoá ảnh cũ nếu tồn tại
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }

            // Lưu ảnh mới vào thư mục services
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        // Lấy số trang từ request
        $currentPage = $request->input('page', 1);

        return redirect()->route('services.index', ['page' => $currentPage])
            ->with('success', 'Cập nhật thành công');
    }

    public function destroy($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa dịch vụ.'
            ]);
        }

        // Lấy cả dịch vụ đã bị xóa mềm
        $service = Service::withTrashed()->findOrFail($id);

        // Kiểm tra nếu dịch vụ đã từng được sử dụng
        $usedInAppointments = Appointment::where('service_id', $service->id)
            ->orWhereJsonContains('additional_services', $service->id)
            ->exists();

        $usedInCancelled = CancelledAppointment::where('service_id', $service->id)
            ->orWhereJsonContains('additional_services', $service->id)
            ->exists();

        if ($usedInAppointments || $usedInCancelled) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xoá vĩnh viễn vì dịch vụ đã được sử dụng trong lịch hẹn hoặc lịch sử hủy.'
            ]);
        }

        // Xóa ảnh nếu tồn tại
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá vĩnh viễn dịch vụ.'
        ]);
    }


    public function softDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa dịch vụ.'
            ]);
        }

        $service = Service::findOrFail($id);
        $service->delete();
        return response()->json(['success' => true, 'message' => 'Đã xoá mềm dịch vụ.']);
    }


    public function restore($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa dịch vụ.'
            ]);
        }
        $service = Service::withTrashed()->findOrFail($id);
        $service->restore();
        return response()->json(['success' => true, 'message' => 'Khôi phục dịch vụ thành công.']);
    }
}
