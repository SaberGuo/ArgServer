<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Base
{
    //
    const TABLE = 'pictures';
    protected $table = self::TABLE;

    protected $fillable = ['type','picture_id', 'url','owner_id'];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function plots(){
      return $this->hasMany('App\Models\Plot');
    }


}
