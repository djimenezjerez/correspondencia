<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
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
        /** @var \App\Models\User */
        $user = Auth::user();
        if ($this->method() == 'POST' && $user->can('CREAR USUARIO')) {
            return true;
        } elseif ((in_array($this->method(), ['PATCH', 'PUT'])) && ($this->user->id == $user->id || $user->can('EDITAR USUARIO'))) {
            return true;
        } else {
            return false;
        }
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
            'last_name' => 'alpha_spaces|min:3',
            'identity_card' => 'alpha_dash|min:4',
            'email' => 'email:rfc',
            'address' => 'string|min:3',
            'phone' => 'numeric',
            'area_id' => 'exists:areas,id',
        ];
        switch ($this->method()) {
            case 'POST': {
                $rules['username'] = 'alpha_num|min:3|unique:users,username';
                $rules['identity_card'] .= '|unique:users,identity_card';
                foreach ($rules as $key => $rule) {
                    $rules[$key] = implode('|', ['required', $rule]);
                }
            }
            case 'PUT':
            case 'PATCH': {
                if (($this->id == auth()->user()->id)) {
                    $rules = [
                        'old_password' => 'string|min:4',
                        'password' => 'string|min:4',
                    ] + $rules;
                    $rules[] = [
                        'username' => '',
                    ];
                    foreach (array_slice($rules, 2) as $key => $rule) {
                        $rules[$key] = 'prohibited';
                    }
                    foreach (array_slice($rules, 0, 2) as $key => $rule) {
                        $rules[$key] = implode('|', ['required', $rule]);
                    }
                } else {
                    $rules['username'] = 'alpha_num|min:3';
                    foreach ($rules as $key => $rule) {
                        $rules[$key] = implode('|', ['sometimes|required', $rule]);
                    }
                }
            }
        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        if (isset($this->username)) {
            $this->merge([
                'username' => trim(mb_strtoupper($this->username)),
            ]);
        }
        if (isset($this->identity_card)) {
            $this->merge([
                'identity_card' => trim(mb_strtoupper($this->identity_card)),
            ]);
        }
    }
}
