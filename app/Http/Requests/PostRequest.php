<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Có thể chỉnh theo nhu cầu phân quyền
    }

    public function rules(): array
    {
        $postId = $this->route('post')?->id ?? null;

        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'short_description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'slug.max' => 'Slug không được vượt quá 255 ký tự.',
            'slug.unique' => 'Slug này đã được sử dụng, vui lòng chọn slug khác.',
            'short_description.max' => 'Mô tả ngắn không được vượt quá 500 ký tự.',
            'content.required' => 'Nội dung bài viết không được để trống.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, svg, webp.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
            'status.required' => 'Trạng thái bài viết là bắt buộc.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
            'published_at.date' => 'Ngày xuất bản phải là ngày hợp lệ.',
        ];
    }
}
