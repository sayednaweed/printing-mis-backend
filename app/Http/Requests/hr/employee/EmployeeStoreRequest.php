<?php

namespace App\Http\Requests\hr\employee;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeStoreRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'father_name' => 'required',
            'date_of_birth' => 'required',
            'gender_id' => 'required',
            'marital_status_id' => 'required',
            'nationality_id' => 'required',
            'permanent_province_id' => 'required',
            'permanent_district_id' => 'required',
            'current_province_id' => 'required',
            'current_district_id' => 'required',
            'family_contact' => 'required|unique:contacts,value',
            'contact' => 'required|unique:contacts,value',
            'permanent_area' => 'required',
            'current_area' => 'required',
            'hire_type_id' => 'required',
            'overtime_rate' => 'required',
            'department_id' => 'required',
            'position_id' => 'required',
            'hire_date' => 'required',
            'currency_id' => 'required',
            'salary' => 'required',
            'shift_id' => 'required',
            'has_attachment' => 'required',
            'nid_type_id' => 'required',
            'nid_province_id' => 'integer',
            'nid_number' => 'required',
            'nid_volume' => 'string',
            'nid_page' => 'string',
            'education_id' => 'required',
            'description' => 'string',
        ];
    }
}
