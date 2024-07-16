<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
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
            'phone' => ['required', 'regex:/(\+\d+)(\d{9})$/'],
            'otp' => ['required','numeric'],
            // 'type' => ['required', 'string', 'in:USER_SIGNUP,FORGOT_PASSWORD'],    
        ];
    }

    /**
     * Custom message validate
     * @return array
     */
    public function messages()
    {
        return [
            'type.in' => 'The type is USER_GET_OTP or USER_SIGNUP or FORGOT_PASSWORD',
            'phone.regex' => 'The phone is format with +1,+84,+22...',
        ];
    }
}
