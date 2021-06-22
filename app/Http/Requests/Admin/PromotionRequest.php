<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class PromotionRequest extends FormRequest
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
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'student_user_id' => ['required', 'integer', 'exists:users,id'],
            'instructor_user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
    
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if (!$this->user()->hasAnyRole(['country-manager', 'latam-manager','admin'])) {
            $this->merge([
                'instructor_user_id' => $this->user()->id,
            ]);
        }
    }    
}
