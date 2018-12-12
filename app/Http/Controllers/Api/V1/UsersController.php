<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\User;

use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Api\UserSearchRequest;
use App\Http\Requests\Api\UserInviteRequest;
use App\Http\Requests\Api\ResetPassRequest;

use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;
use App\Traits\PassportToken;
use TomLingham\Searchy\Facades\Searchy;


class UsersController extends Controller
{
    //
    use PassportToken;


    public function resetPass(ResetPassRequest $request){
      $resetData = \Cache::get($request->resetPass_key);
      if(!$resetData){
        return $this->response->error('验证码已经失效', 422);
      }
      if (!hash_equals($resetData['code'], $request->resetPass_code)) {
          // 返回401
          return $this->response->errorUnauthorized('验证码错误');
      }
      $user = User::where('phone','=',$resetData['phone'])->first();
      $user->password = bcrypt($request->password);
      $user->save();
      return $this->response->array($user)->setStatusCode(201);

    }
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            $request->type => $request[$request->type],
            
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        Auth::login($user);

        $result = $this->getBearerTokenByUser($user, '1', false);
        return $this->response->array($result)->setStatusCode(201);

    }
    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }
    public function orgInvite(UserInviteRequest $request){
        $owner_id = $this->user()->id;

    }

    public function search(UserSearchRequest $request){
      $colName = $request->colName;
      $searchContent = $request->content;

      //$this->response->collection(\App\Models\User::hydrate(Searchy::search('users')->fields($colName)->query($searchContent)->get()), new UserTransformer());
      return $this->response->collection(\App\Models\User::search($searchContent)->get(), new UserTransformer());
    }
}
