<?php

namespace App\Http\Requests\template\user;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            "id" => ['required'],
            "full_name" => ['required', "string", "min:3", "max:45"],
            "username" => ['required', "string", "min:3", "max:45"],
            "email" => ["required", "email"],
            "job_id" => ["required", "string"],
            "destination_id" => ["required", "string"],
            "status" => ["required"],
            'province_id' => 'required|exists:provinces,id',
            'gender_id' => 'required|exists:genders,id',
            'zone_id' => 'required|exists:zones,id',
        ];
    }
}
