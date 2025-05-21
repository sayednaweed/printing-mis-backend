<?php

namespace App\Http\Requests\app\expense;

use Illuminate\Foundation\Http\FormRequest;

class PartyStoreRequest extends FormRequest
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
            'email' => 'required',
            'contact' => 'required',
            'company_name' => 'required',
            'name' => 'required',
            'gender_id' => 'required',
            'nationality_id' => 'required',
        ];
    }
}
