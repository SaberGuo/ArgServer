<?php

namespace App\Models;
use App\Models\Picture;
use Illuminate\Database\Eloquent\Model;

class Land extends Base
{
    //
    const TABLE = 'lands';
    protected $table = self::TABLE;

    public static $tp = "land";

    protected $fillable = ['land_id','code', 'lat', 'lng', 'alt','type','investigator_name','investigated_at','data','uploaded_at'];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function delete(){
      $this->plots()->delete();

      return parent::delete();
    }

    public function plots(){
      return $this->hasMany('App\Models\Plot');
    }
    public function pictures(){
      return Picture::where('type','=','land')->where('owner_id','=',$this->id)->get();
    }

    public function app_id(){
      return $this->land_id;
    }


}
