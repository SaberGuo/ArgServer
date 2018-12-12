<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;
use NotificationChannels\WebPush\HasPushSubscriptions;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasApiTokens;
    use Searchable;
    use HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone','email', 'password','weixin_openid', 'weixin_unionid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Rest omitted for brevity

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getFullNameAttribute($v){
      return $this->attributes['id']."-".$this->attributes['name'];
    }
    public function download_jobs()
    {
        return $this->hasMany('App\Models\DownloadJob');
    }

    public function findForPassport($username)
    {
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
          $credentials['email'] = $username :
          $credentials['phone'] = $username;

        return self::where($credentials)->first();
    }

    public function devices(){
      return $this->belongsToMany('App\Models\Device')->withPivot(['urole_ids']);
      //return $this->hasMany('App\Models\Device')->withPivot(['urole_ids']);
    }

    public function organizations(){
      return $this->belongsToMany('App\Models\Organization')->withPivot(['urole_ids']);
    }

    public function has($model, $id, $isCascading=false) {
        $res = false;
        if($isCascading){
          foreach ($this->$model as $instance) {
              if ($instance->id == $id) {
                  return true;
              }
              $res|=$instance->hasSub($id);
              if($res){
                return $res;
              }
          }
          return $res;
        }

        foreach ($this->$model as $instance) {
            if ($instance->id == $id) {
                return true;
            }
        }
        return false;
    }

    public function allows($permission,$roles,$requireAll = false)
    {
        //Log::info($permission);
        if (is_array($permission)) {
            foreach ($permission as $permName) {
                $hasPerm = $this->allows($permName, $roles, false);

                if ($hasPerm && !$requireAll) {
                    return true;
                } elseif (!$hasPerm && $requireAll) {
                    return false;
                }
            }

            // If we've made it this far and $requireAll is FALSE, then NONE of the perms were found
            // If we've made it this far and $requireAll is TRUE, then ALL of the perms were found.
            // Return the value of $requireAll;
            return $requireAll;
        } else {
            foreach ($roles as $role) {
                // Validate against the Permission table
                //if ($role->app_id != $app_id) continue;
                foreach ($role->permissions()->get() as $perm) {
                    if ($perm->name == $permission) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
