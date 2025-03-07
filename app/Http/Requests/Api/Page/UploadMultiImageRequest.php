<?php

namespace App\Http\Requests\Api\Page;

use Illuminate\Foundation\Http\FormRequest;

class UploadMultiImageRequest extends FormRequest
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
            'images' => 'required|array',
            'images.*' => 'mimes:jpeg,jpg,png,gif'
        ];
    }
}
