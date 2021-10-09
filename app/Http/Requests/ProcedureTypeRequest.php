<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureTypeRequest extends FormRequest
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
            'code' => 'string|min:2|required',
            'requirements' => 'array|sometimes',
            'requirements.*' => 'exists:requirements,id',
        ];
        if ($this->method() == 'POST') {
            $rules['code'] .= '|unique:procedure_types,code';
        }
        return $rules;
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
