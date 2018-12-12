<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Support\Facades\Log;


class Controller extends BaseController
{
    //
    use Helpers;
    public function _index($roles, $where = null, $callback = null)
    {
      //$this->assertPermissions('index', $roles);
      if ($where === null) {
          $where = [DB::raw('1'), 1];
      }
      $items = call_user_func_array([static::$model, 'where'], $where);
      $with = Request::input('with');
      if ($with) {
          if (str_contains($with,',')) {
              $with = explode(',', $with);
          }
          $items = $items->with($with);
      }
      if ($callback) {
          $callback($items);
      }
      return $items->get();
    }
    public function _store($data, $roles) {

        //$this->assertPermissions('store', $roles);
        return call_user_func([static::$model, 'create'], $data);
    }

    public function _update($id, $data, $roles) {
        //$this->assertPermissions('update', $roles);
        $model = call_user_func([static::$model, 'find'], $id);
        $model->fill($data);
        $model->save();
        return $model;
    }
    /*public function assertPermissions($action, $roles) {

        if (!empty(static::$permissions)) {
            $permissions = isset(static::$permissions[$action]) ? static::$permissions[$action]: @static::$permissions['all'];
            if (!$permissions) return;

            $this->user()->allows($permissions, $roles,false) || $this->response->errorUnauthorized("权限不足");
        }
    }*/
}
