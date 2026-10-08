<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CategoryRequest
 * Validasi untuk form kategori
 */
class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya admin yang boleh mengakses
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name'        => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'description' => 'nullable|string',
            'status'      => 'nullable|in:active,inactive',
            'is_active'   => 'nullable|boolean',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:10240',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi',
            'name.unique'   => 'Nama kategori sudah ada',
            'image.image'   => 'File yang diupload harus berupa gambar',
            'image.mimes'   => 'Format gambar harus jpeg, png, jpg, webp, atau svg',
            'image.max'     => 'Ukuran gambar maksimal 10MB',
        ];
    }
}
