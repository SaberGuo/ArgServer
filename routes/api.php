<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

$api = app('Dingo\Api\Routing\Router');


$api->version('v1', function($api) {
    $api->get('version', function() {
        return response('this is version v1');
    });
});

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'serializer:array'
], function($api) {
  $api->group([
      'middleware'=>'api.throttle',
      'limit'=>config('api.rate_limits.sign.limit'),
      'expires'=>config('api.rate_limits.sign.expires'),
    ],function($api){

      $api->post('reset/password','UsersController@resetPass')->name('api.users.resetPass');
      $api->post('reset/code','VerificationCodesController@resetPass')->name('api.verificationCodes.resetPass');
      // 短信验证码
      $api->post('verificationCodes', 'VerificationCodesController@store')
          ->name('api.verificationCodes.store');

      // 用户注册
      $api->post('users', 'UsersController@store')
          ->name('api.users.store');
      // 图片验证码
      $api->post('captchas', 'CaptchasController@store')
          ->name('api.captchas.store');
      //$api->post('captchas/email', 'CaptchasController@storeByEmail')
      //    ->name('api.captchas.email.store');
      // 第三方登录
      $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->name('api.socials.authorizations.store');

            $api->group(['middleware' => 'api.auth'], function($api) {
                // 当前登录用户信息
                $api->get('user', 'UsersController@me')
                        ->name('api.user.show');
                // 数据上传
                $api->post('data','DatasController@store')->name('api.datas.store');

                // 样地数据
                $api->get('/data/lands/{land_id}','DatasController@showLand')->name('api.datas.showLand');
                $api->get('/data/lands','DatasController@indexLand')->name('api.datas.indexLand');
                $api->post('data/lands','DatasController@storeLand')->name('api.datas.storeLand');
                $api->put('data/lands/{land_id}','DatasController@updateLand')->name('api.datas.updateLand');
                $api->delete('data/lands/{land_id}','DatasController@deleteLand')->name('api.datas.deleteLand');
                // 样方数据
                $api->get('/data/lands/{land_id}/plots/{plot_id}','DatasController@showPlot')->name('api.datas.showPlot');
                $api->get('/data/lands/{land_id}/plots','DatasController@indexPlot')->name('api.datas.indexPlot');
                $api->post('data/lands/{land_id}/plots','DatasController@storePlot')->name('api.datas.storePlot');
                $api->put('data/lands/{land_id}/plots/{plot_id}','DatasController@updatePlot')->name('api.datas.updatePlot');
                $api->delete('data/lands/{land_id}/plots/{plot_id}','DatasController@deletePlot')->name('api.datas.deletePlot');
                // 物种数据
                $api->get('/data/lands/{land_id}/plots/{plot_id}/species/{specie_id}','DatasController@showSpecie')->name('api.datas.showSpecie');
                $api->get('data/lands/{land_id}/plots/{plot_id}/species','DatasController@indexSpecie')->name('api.datas.indexSpecie');
                $api->post('data/lands/{land_id}/plots/{plot_id}/species','DatasController@storeSpecie')->name('api.datas.storeSpecie');
                $api->put('data/lands/{land_id}/plots/{plot_id}/species/{specie_id}','DatasController@updateSpecie')->name('api.datas.updateSpecie');
                $api->delete('data/lands/{land_id}/plots/{plot_id}/species/{specie_id}','DatasController@deleteSpecie')->name('api.datas.deleteSpecie');
                // 样点数据
                $api->get('data/points','DatasController@indexPoint')->name('api.datas.indexPoint');
                $api->get('data/points/{point_id}','DatasController@showPoint')->name('api.datas.showPoint');
                $api->post('data/points','DatasController@storePoint')->name('api.datas.storePoint');
                $api->put('data/points/{point_id}','DatasController@updatePoint')->name('api.datas.updatePoint');
                $api->delete('data/points/{point_id}','DatasController@deletePoint')->name('api.datas.deletePoint');

                $api->get('data/pictures/{type}/{owner_id}','PictureController@index')->name('api.pictures.index');
                $api->get('data/pictures/{picture_id}','PictureController@show')->name('api.pictures.show');
                $api->post('data/pictures','PictureController@store')->name('api.pictures.store');
                $api->put('data/pictures/{picture_id}','PictureController@update')->name('api.pictures.update');
                $api->delete('data/pictures/{picture_id}','PictureController@delete')->name('api.pictures.delete');
            });
      });
      // 登录
      $api->post('authorizations', 'AuthorizationsController@store')
      ->name('api.authorizations.store');
      // 刷新token
      $api->put('authorizations/current', 'AuthorizationsController@update')
          ->name('api.authorizations.update');
      // 删除token
      $api->delete('authorizations/current', 'AuthorizationsController@destroy')
          ->name('api.authorizations.destroy');


});
