<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => ['required'],
            'type' => ['required', 'string', 'in:USER_GET_OTP,USER_SIGNUP,FORGOT_PASSWORD'],
            'recaptcha_token' => ['required', 'string']
        ];
    }

    /**
     * Custom message validate
     * @return array
     */
    public function messages()
    {
        return [
            'phone.regex' => 'The phone is format with +1,+84,+22...',
            'type.in' => 'The type is USER_GET_OTP or USER_SIGNUP or FORGOT_PASSWORD'
        ];
    }
}
