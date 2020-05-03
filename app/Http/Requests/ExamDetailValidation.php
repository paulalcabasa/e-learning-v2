<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamDetailValidation extends FormRequest
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
            'exam_schedule_id' => 'required|integer',
            'dealer_id'        => 'required|integer',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date',
            'timer'            => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'timer.integer' => 'The timer must be a number.'
        ];
    }
}
