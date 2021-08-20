<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CodeRequest extends FormRequest
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
            'code' => 'required|exists:procedures,code'
        ];
    }

    protected function prepareForValidation()
    {
        if (isset($this->code)) {
            $this->merge([
                'code' => trim(mb_strtoupper($this->code)),
            ]);
        }
    }
}
