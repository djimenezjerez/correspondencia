<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->procedure->owner;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attachments' => 'array|min:1|max:5|required',
            'attachments.*' => 'mimes:jpg,jpeg,bmp,png,gif,doc,docx,pdf|max:4000',
        ];
    }
}
