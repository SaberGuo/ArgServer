<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plot extends Base
{
    //
    const TABLE = 'plots';
    protected $table = self::TABLE;

    static $type = 'plot';

    protected $fillable = ['plot_id','code', 'lat', 'lng', 'alt','type','investigator_name','investigated_at','data','uploaded_at'];

    public function land() {
        return $this->belongsTo('App\Models\Land', 'land_id','land_id');
    }

    public function species(){
      return $this->hasMany('App\Models\Specie','plot_id','plot_id');
    }

    public function owners(){
      return $this->belongsToMany('App\Models\Plot','plots_rels','plot_id','owner_id');
    }

    public function pictures(){
      return Pictures::where('type','=','plot')->where('owner_id','=',$this->id)->get();
    }
}
