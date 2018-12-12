<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Requests\Api\CaptchaRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CaptchasController extends Controller
{
    //
    public function store(CaptchaRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $value = "";
        if($request->type == "phone"){
          if(!$request->phone){
            return $this->response->errorInternal("phone 字段不存在");
          }
          if(User::where('phone',$request->phone)->first()){
            return $this->response->errorInternal("用户已存在");
          }
          $value = $request->phone;
        }

        if($request->type == "email"){
          if(!$request->email){
            return $this->response->errorInternal("email 字段不存在");
          }
          if(User::where('email',$request->email)->first()){
            return $this->response->errorInternal("用户已存在");
          }
          $value = $request->email;
        }


        $key = 'captcha-'.$request->type.str_random(15);

        $captcha = $captchaBuilder->build();
        $expiredAt = now()->addMinutes(10);
        \Cache::put($key, [$request->type => $value, 'code' => $captcha->getPhrase()], $expiredAt);

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ];

        return $this->response->array($result)->setStatusCode(201);
    }

}
