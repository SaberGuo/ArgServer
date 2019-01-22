<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specie extends Base
{
    //
    const TABLE = 'species';
    protected $table = self::TABLE;

    static $type = "specie";

    protected $fillable = ['specie_id','code', 'lat', 'lng', 'alt','type','name','latin_name','data','uploaded_at'];

    public function plot() {
        return $this->belongsTo('App\Models\Plot', 'plot_id','plot_id');
    }
    public function pictures(){
      return Pictures::where('type','=','specie')->where('owner_id','=',$this->id)->get();
    }


}
