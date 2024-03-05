<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class   EmployeeRequest extends FormRequest
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
            'name'     => 'required|string|max:255',
            'number'   => 'required|string|max:255',
            'job_type' => 'required|string|max:255',
        ];
    }

    public function messages(){
        return [
            'name.required'     => 'Please enter the name',
            'number.required'   => 'Please enter the number',
            'job_type.required' => 'Please enter the job_type',
        ];
    }
}
