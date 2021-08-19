<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'username' => 'required|min:3',
            'password' => 'required|min:4',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'username' => trim(mb_strtoupper($this->username)),
        ]);
    }
}
