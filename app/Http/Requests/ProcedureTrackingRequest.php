<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcedureTrackingRequest extends FormRequest
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
        $search = ($this->search_by == 'created_at') ? 'required|date_format:Y-m-d' : 'required|string|min:2';

        return [
            'search' => $search,
            'search_by' => 'required|in:code,origin,detail,created_at',
        ];
    }
}
