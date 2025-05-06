<?php

namespace App\Http\Requests\app\hr;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends FormRequest
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
            'id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'father_name' => 'required',
            'date_of_birth' => 'required',
            'gender_id' => 'required',
            'contact' => 'required',
            'email' => 'required',
            'marital_status_id' => 'required',
            'nationality_id' => 'required',
            'permanent_province_id' => 'required',
            'permanent_district_id' => 'required',
            'current_province_id' => 'required',
            'current_district_id' => 'required',
            'permanent_area' => 'required',
            'current_area' => 'required',
            'hire_type_id' => 'required',
            'department_id' => 'required',
        ];
    }
}
