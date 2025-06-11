<?php

namespace App\Http\Requests\hr\attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
            'attendances' => 'required|array',
            'shift_id' => 'required',
            'attendances.*.employee_id' => 'required|exists:employees,id',
            'attendances.*.description' => 'nullable|string',
            'attendances.*.status_type_id' => 'required|exists:attendance_statuses,id',
        ];
    }
}
