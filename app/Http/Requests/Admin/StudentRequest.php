<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class StudentRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->student)],
            'phone' => ['required', 'string'],
            'document_number' => ['required', 'string'],
            'birthday' => ['required'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'academy_id' => ['required', 'integer', 'exists:academies,id'],
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'active' => ['boolean'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'password' => ['required_with:change_password', 'nullable', 'string', new Password, 'confirmed'],   
        ];
    }
    
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if (!$this->user()->hasAnyRole(['latam-manager','admin'])) {
            $this->merge([
                'country_id' => $this->user()->city->country_id,
            ]);
        }
        
        if (!$this->user()->hasAnyRole(['country-manager', 'latam-manager','admin'])) {
            $this->merge([
                'academy_id' => $this->user()->academy_id,
            ]);
        }        
    }    
}
