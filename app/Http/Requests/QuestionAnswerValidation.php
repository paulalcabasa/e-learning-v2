<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionAnswerValidation extends FormRequest
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
            'sub_module_id' => 'required',
            'question'      => 'required',
            // 'choice_letter' => 'required|in['a', 'b', 'c', 'd']',
            'is_correct'    => 'numeric'
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
