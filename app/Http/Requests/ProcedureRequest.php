<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProcedureRequest extends FormRequest
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
        if ($this->method() == 'POST' && $user->can('CREAR TRÁMITE')) {
            return true;
        } elseif (in_array($this->method(), ['PATCH', 'PUT']) && !$this->procedure->archived && $this->procedure->owner && $user->can('EDITAR TRÁMITE')) {
            return true;
        } else {
            return false;
        }
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
            'code' => 'string|min:2',
            'origin' => 'string|min:3',
            'detail' => 'string|min:3',
            'procedure_type_id' => 'exists:procedure_types,id',
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach ($rules as $key => $rule) {
                    $rules[$key] = implode('|', ['required', $rule]);
                }
            }
            case 'PUT':
            case 'PATCH': {
                foreach ($rules as $key => $rule) {
                    $rules[$key] = implode('|', ['sometimes|required', $rule]);
                }
            }
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
