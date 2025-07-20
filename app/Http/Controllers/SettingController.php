<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');

        // Lấy social links theo key cố định
        $social_keys = ['youtube', 'facebook', 'instagram', 'tiktok'];
        $social_links = [];
        foreach ($social_keys as $k) {
            $social_links[$k] = data_get($settings->get($k), 'value', '');
        }

        // Lấy images theo key cố định
        $image_keys = ['anh_dang_nhap', 'anh_dang_ky', 'bang_gia'];
        $images = [];
        foreach ($image_keys as $k) {
            $images[$k] = [
                'key'   => $k,
                'value' => data_get($settings->get($k), 'value', ''),
            ];
        }

        return view('admin.settings.index', compact('social_links', 'images'));
    }


    public function saveSettings(Request $request)
    {
        // Xử lý social links
        $social_keys = ['youtube', 'facebook', 'instagram', 'tiktok'];
        foreach ($social_keys as $key) {
            $value = $request->input("social_links.{$key}.value", '');
            Setting::updateOrCreate(
                ['key' => $key, 'type' => 'url'],
                ['value' => $value]
            );
        }

        // Xử lý hình ảnh
        $image_keys = ['anh_dang_nhap', 'anh_dang_ky', 'bang_gia'];
        foreach ($image_keys as $key) {
            $existing = $request->input("images.{$key}.existing_value");
            $file     = $request->file("images.{$key}.value");

            // Nếu có upload file mới
            if ($file && $file->isValid()) {
                $path = $file->store('uploads', 'public');
                // Xoá file cũ nếu có
                if ($existing && Storage::disk('public')->exists($existing)) {
                    Storage::disk('public')->delete($existing);
                }
            } else {
                // giữ nguyên
                $path = $existing;
            }

            Setting::updateOrCreate(
                ['key' => $key, 'type' => 'image'],
                ['value' => $path]
            );
        }

        return response()->json(['success' => true, 'message' => 'Cài đặt đã được lưu!']);
    }
}
