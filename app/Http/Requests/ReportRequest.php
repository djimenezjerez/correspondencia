<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
            'start_date' => 'required|date_format:Y-m-d|before_or_equal:'.$this->end_date,
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:'.$this->start_date,
            'area_id' => 'sometimes|nullable|exists:areas,id',
            'procedure_type_id' => 'sometimes|nullable|exists:procedure_types,id',
            'search_by' => 'required|in:pending,received,archived',
        ];
    }
}
