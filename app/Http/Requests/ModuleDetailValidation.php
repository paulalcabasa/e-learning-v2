<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuleDetailValidation extends FormRequest
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
			'dealer_id'  => 'required',
			'module_id'  => 'required',
			'start_date' => 'required|date|after_or_equal:' . date('Y-m-d'),
			'end_date'   => 'required|date|different:start_date|after:start_date',
			'status' 	 => Rule::in(['waiting', 'on_progress', 'ended']),
			'is_opened'  => 'boolean',
			'is_enabled' => 'boolean',
			'is_active'  => 'boolean'
		];
	}

	public function messages() 
	{
		return [
			
		];
	}
}
