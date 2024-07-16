<?php

namespace App\Http\Requests\Admin\Payout;

use Illuminate\Foundation\Http\FormRequest;

class AddcardRequest extends FormRequest
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
            'bank_name' => ['required', 'string'],
            'fullname' => ['required', 'string'],
            'bank_no' => ['required', 'string'],
        ];
    }
}
