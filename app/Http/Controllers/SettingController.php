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
        $settings = Setting::all();
        $social_links = $settings->where('type', 'url')->map(function ($item) {
            return $item->toArray(); // Chuyển đổi thành mảng
        });
        $images = $settings->where('type', 'image')->map(function ($item) {
            return $item->toArray(); // Chuyển đổi thành mảng
        });

        // Debug để kiểm tra
        // dd($social_links->toArray());

        return view('admin.settings.index', compact('social_links', 'images'));
    }

    public function saveSettings(Request $request)
    {


        // Handle social links
        $existingLinks = Setting::where('type', 'url')->pluck('value', 'key')->toArray();
        $newLinks = $request->input('social_links', []);

        foreach ($newLinks as $link) {
            Setting::updateOrCreate(
                ['key' => $link['key'], 'type' => 'url'],
                ['value' => $link['value']]
            );
            if (isset($existingLinks[$link['key']])) {
                unset($existingLinks[$link['key']]);
            }
        }
        Setting::where('type', 'url')->whereIn('key', array_keys($existingLinks))->delete();

        // Handle images
        $existingImages = Setting::where('type', 'image')->pluck('value', 'key')->toArray();
        $submittedImages = $request->input('images', []);
        $uploadedFiles = $request->file('images', []);

        foreach ($submittedImages as $index => $imageData) {
            $key = $imageData['key'];
            $existingValue = $imageData['existing_value'] ?? null;

            // If a new file is uploaded
            if (isset($uploadedFiles[$index]['value']) && $uploadedFiles[$index]['value']->isValid()) {
                // Store new file
                $path = $uploadedFiles[$index]['value']->store('uploads', 'public');
                // Delete old file if it exists
                if ($existingValue && Storage::disk('public')->exists($existingValue)) {
                    Storage::disk('public')->delete($existingValue);
                }
            } else {
                // No new file, keep existing value if available
                $path = $existingValue;
            }

            if ($path) {
                Setting::updateOrCreate(
                    ['key' => $key, 'type' => 'image'],
                    ['value' => $path]
                );
            }
        }

        // xoá các hình ảnh đã bị xoá
        if ($request->has('images')) {
            $submittedKeys = array_column($submittedImages, 'key');
            $keysToDelete = array_diff(array_keys($existingImages), $submittedKeys);

            foreach ($keysToDelete as $key) {
                $setting = Setting::where('key', $key)->where('type', 'image')->first();
                if ($setting && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }
                $setting?->delete();
            }
        }


        return redirect()->back()->with('success', 'Cài đặt đã được lưu!');
    }
}
