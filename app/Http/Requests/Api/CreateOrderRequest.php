<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'service_name' => 'required|string',
            'service_child_name' => 'required|string',
            'address' => 'required|string',
            'longtitude'  => 'required',
            'latitude'    => 'required',
            // 'longtitude'  => ['required', 'numeric', 'regex:/^(\+|-)?(?:180(?:(?:\.0{1,8})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,8})?))$/'],
            // 'latitude'    => ['required', 'numeric', 'regex:/^(\+|-)?(?:90(?:(?:\.0{1,8})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,8})?))$/'],
            'token_payment' => ['required', 'string'],
        ];
    }
}
