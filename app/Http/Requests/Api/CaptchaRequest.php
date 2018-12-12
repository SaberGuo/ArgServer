<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class CaptchaRequest extends FormRequest
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
          'type' => 'required|in:phone,email',
          'phone' => 'required_if:type,phone|regex:/^1[34578]\d{9}$/|unique:users,phone',
          'email' => 'required_if:type,email|email|unique:users,email'
      ];
    }
}
