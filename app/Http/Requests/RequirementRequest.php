<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequirementRequest extends FormRequest
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
        $rules = [
            'name' => 'string|min:3|required',
        ];
        if ($this->method() == 'POST') {
            $rules['name'] .= '|unique:requirements,name';
        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        if (isset($this->name)) {
            $this->merge([
                'name' => trim(mb_strtoupper($this->name)),
            ]);
        }
    }
}
