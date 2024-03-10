<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
            'posts.*.id' => 'integer',
            'posts.*.title' => 'required|string',
            'posts.*.description' => 'required|string'
        ];
    }

    public function messages() :array
    {
        return [
            'posts.*.title.required' => 'Please enter the title',
            'posts.*.description.required' => 'Please enter the description'
        ];
    }
}
