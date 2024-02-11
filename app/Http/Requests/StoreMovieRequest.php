<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'country' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'year' => 'nullable|integer|min:1900|max:2100',
            'duration' => 'nullable|integer|min:1|max:3000',
            'director' => 'nullable|string|max:255',
            'screenplay' => 'nullable|string|max:255',
            'genres' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:10',
        ];
    }
}
