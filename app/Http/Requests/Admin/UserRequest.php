<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user)],
            'phone' => ['required', 'string'],
            'document_number' => ['required', 'string'],
            'birthday' => ['required'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'role_id' => ['required'],
            'academy_id' => ['required', 'integer', 'exists:academies,id'],
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'active' => ['boolean'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'password' => ['required_with:change_password', 'nullable', 'string', new Password, 'confirmed'],   
        ];
    }
}
