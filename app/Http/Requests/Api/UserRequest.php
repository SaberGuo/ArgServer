<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class UserRequest extends FormRequest
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
          'name' => 'required|string|max:255',
          'type' => 'required|in:email,phone',
          'email' => 'required_if:type,email|email|unique:users,email',
          'phone' => 'required_if:type,phone|regex:/^1[34578]\d{9}$/|unique:users,phone',
          'password' => 'required|string|min:6',
          'verification_key' => 'required|string',
          'verification_code' => 'required|string',
      ];
    }
    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
        ];
    }
}
