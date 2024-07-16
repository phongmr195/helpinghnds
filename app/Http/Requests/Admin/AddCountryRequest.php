<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AddCountryRequest extends FormRequest
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
            'alt' => 'required',
            'title' => 'required',
            'phone_code' => ['required', 'string', 'max:3', 'regex:/(\+\d+)(\d{9})$/']
        ];
    }

    /**
     * Custom message validate
     * @return array
     */
    public function messages()
    {
        return [
            'phone_code.regex' => 'The phone is format with +1,+84,+22,...'
        ];
    }
}
