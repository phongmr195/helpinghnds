<?php

namespace App\Http\Requests\Admin\Payout;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmCashoutRequest extends FormRequest
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
            'password' => ['required']
        ];
    }

    /**
     * Custom output message
     * 
     * @return array
     */
    public function messages()
    {
        return [
            'password.required' => 'Mật khẩu không được để trống'
        ];
    }
}
