<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Land extends Base
{
    //
    const TABLE = 'lands';
    protected $table = self::TABLE;

    public static $tp = "land";

    protected $fillable = ['land_id','code', 'lat', 'lng', 'alt','type','investigator_name','investigated_at','data','uploaded_at'];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function plots(){
      return $this->hasMany('App\Models\Plot');
    }
    public function pictures(){
      return Pictures::where('type','=','land')->where('owner_id','=',$this->id)->get();
    }


}
