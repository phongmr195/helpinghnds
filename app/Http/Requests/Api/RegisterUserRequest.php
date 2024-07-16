<?php

namespace App\Http\Requests\Api;

use App\Rules\ClientPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'fullname' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'max:36'],
            'phone' => ['required', 'string', 'max:15', new ClientPhoneNumber],
        ];
    }

    /**
     * Custom message validate
     * @return array
     */
    public function messages()
    {
        return [
            'phone.regex' => 'The phone format is with +1,+84,+22...'
        ];
    }
}
