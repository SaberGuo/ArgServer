<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use App\Http\Requests\Api\VerificationCodeRequest;
use App\Http\Requests\Api\ResetCodeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\register;

class VerificationCodesController extends Controller
{
    public function resetPass(ResetCodeRequest $request, EasySms $easySms){
      $phone = $request->phone;
      $user = User::where('phone','=',$phone)->first();
      if(!$user){
        return $this->response->error('无此用户', 422);
      }

      if(env('API_DEBUG') == true){
        $code = '1234';
      }
      else{
        // 生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
        try {
            $result = $easySms->send($phone, [
              'template'=>'SMS_130745118',
              'data'=>['code'=>$code]
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            $response = $exception->getResponse();
            $result = json_decode($response->getBody()->getContents(), true);
            return $this->response->errorInternal($result['msg'] ?? '短信发送异常');
        }

      }
      $key = 'resetCode_'.str_random(15);
      $expiredAt = now()->addMinutes(5);
      // 缓存验证码 10分钟过期。
      \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

      return $this->response->array([
          'key' => $key,
          'expired_at' => $expiredAt->toDateTimeString(),
      ])->setStatusCode(201);
    }
    //
    public function store(VerificationCodeRequest $request, EasySms $easySms)
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

        //$captchaData = \Cache::get($request->captcha_key);
        //if (!$captchaData) {
        //    return $this->response->error('图片验证码已失效', 422);
        //}

        //if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            // 验证错误就清除缓存
            //\Cache::forget($request->captcha_key);
        //    return $this->response->errorUnauthorized('验证码错误');
        //}

        //$phone = $captchaData['phone'];
        $phone = $request->phone;

        if(env('API_DEBUG') == true){
          $code = '1234';
        }
        else{
          // 生成4位随机数，左侧补0
          $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

          try {
              $result = $easySms->send($phone, [
                'template'=>'SMS_130745118',
                'data'=>['code'=>$code]
              ]);
          } catch (\GuzzleHttp\Exception\ClientException $exception) {
              $response = $exception->getResponse();
              $result = json_decode($response->getBody()->getContents(), true);
              return $this->response->errorInternal($result['msg'] ?? '短信发送异常');
          }
        }

        $key = 'verificationCode_'.str_random(15);
        $expiredAt = now()->addMinutes(5);
        // 缓存验证码 10分钟过期。
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
