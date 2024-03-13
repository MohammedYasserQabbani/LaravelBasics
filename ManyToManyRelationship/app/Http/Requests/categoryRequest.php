<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class categoryRequest extends FormRequest
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
            //'categories.*.id' => 'integer',
            'categories.*.name' => 'required|string'
        ];
    }

    public function messages():array 
    {
        return [
            'categories.*.name.required' => 'please enter the Classification type'
        ];
    }
}
