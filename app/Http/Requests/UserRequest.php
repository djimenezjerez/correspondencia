<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        $rules = [
            'name' => 'alpha_spaces|min:3',
            'password' => 'string|min:4',
        ];
        switch ($this->method()) {
            case 'POST': {
                $rules['username'] = 'alpha_num|min:3|unique:users,username';
                foreach (array_slice($rules, 0, 5) as $key => $rule) {
                    $rules[$key] = implode('|', ['required', $rule]);
                }
                return $rules;
            }
            case 'PUT':
            case 'PATCH': {
                $rules['old_password'] = 'string|min:4|sometimes|required';
                $rules['password'] = $rules['password'].'|sometimes|required';
                return $rules;
            }
        }
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'username' => mb_strtolower($this->username),
        ]);
    }
}
