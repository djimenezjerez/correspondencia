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
        if ($this->id == $user->id || $user->can('ACTUALIZAR USUARIO')) {
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
            'password' => 'string|min:4',
            'name' => 'alpha_spaces|min:3',
            'email' => 'email:rfc',
            'address' => 'string|min:3',
            'phone' => 'numeric',
            'document_type_id' => 'exists:document_types,id',
            'area_id' => 'exists:areas,id',
        ];
        switch ($this->method()) {
            case 'POST': {
                $rules['username'] = 'string|min:3|unique:users,username';
                foreach ($rules as $key => $rule) {
                    $rules[$key] = implode('|', ['required', $rule]);
                }
                return $rules;
            }
            case 'PUT':
            case 'PATCH': {
                if (($this->user->id == auth()->user()->id)) {
                    $rules = [
                        'old_password' => 'string|min:4'
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
                    foreach ($rules as $key => $rule) {
                        $rules[$key] = implode('|', ['sometimes|required', $rule]);
                    }
                }
                return $rules;
            }
        }
    }
}
