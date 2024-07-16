<?php

namespace App\Http\Requests\Api;

use App\Rules\WorkerPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterWorkerRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:30'],
            'last_name' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'max:36'],
            'phone' => ['required', 'string', 'max:15', 'regex:/(\+\d+)(\d{9})$/', new WorkerPhoneNumber()],
            'bod' => ['required', 'date_format:Y-m-d'],
            'address' => ['required', 'string', 'max:255'],
            'number_id' => ['required'],
            'type_number_id' => ['required', 'numeric'],
            'img_id_before' => ['required', 'string'],
            'img_id_after' => ['required', 'string'],
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
