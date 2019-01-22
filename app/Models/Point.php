<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Base
{
    //
    const TABLE = 'points';
    protected $table = self::TABLE;

    static $type = 'point';

    protected $fillable = ['point_id','code','lat', 'lng', 'alt','investigator_name','investigated_at','data','uploaded_at'];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function pictures(){
      return Pictures::where('type','=','point')->where('owner_id','=',$this->id)->get();
    }

}
