<?php

namespace App\Http\Requests\app\hr;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateMoreRequest extends FormRequest
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
            'nid_type_id' => 'required|integer',
            'register_no' => 'required|string',
            'register' => 'string',
            'volume' => 'string',
            'page' => 'string',
            'education_level_id' => 'required|integer'
        ];
    }
}
