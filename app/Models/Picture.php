<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Land;
use App\Models\Plot;
use App\Models\Point;
use App\Models\Specie;

class Picture extends Base
{
    //
    const TABLE = 'pictures';
    protected $table = self::TABLE;
    protected static $typeClasses = array(
      'plot'=>Plot::class,
      'land'=>Land::class,
      'species'=>Specie::class,
      'point'=>Point::class);

    protected $fillable = ['type','picture_id', 'url','owner_id'];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function plots(){
      return $this->hasMany('App\Models\Plot');
    }
    public function owner_app_id(){
      $where = ["id","=",$this->owner_id];
      $owner = call_user_func_array([Picture::$typeClasses[$this->type],"where"],$where)->first();
      return $owner->app_id();
    }


}
