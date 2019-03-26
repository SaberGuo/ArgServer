<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Picture;
use App\Models\Land;
use App\Models\Plot;
use App\Models\Point;
use App\Models\Specie;
use App\Transformers\PictureTransformer;

class PictureController extends Controller
{
    protected static $typeClasses = array(
      'plot'=>Plot::class,
      'land'=>Land::class,
      'species'=>Specie::class,
      'point'=>Point::class);

    //
    public function index($type, $owner_id, Request $request){
      $pics = Picture::where('type',$type)->where('owner_id', $owner_id)->get();
      return $this->response->array($pics);
    }

    public function show($pic_id, Request $request){
      $pic = Picture::where('picture_id',$pic_id)->first();
      return $this->response->item($pic);
    }

    public function _storePic($pic, $res){
      $pic->picture_id = $res['picture_id'];

      if($res['type']){
        $pic->type = $res['type'];
      }

      if($res['owner_id']){
        $where = [$pic->type."_id","=",$res['owner_id']];
        $owner = call_user_func_array([PictureController::$typeClasses[$pic->type],"where"],$where)->first();
        if($owner){
          $pic->owner_id = $owner->id;
        }

      }
      if($res['url']){
        $pic->url = $res['url'];
      }
      $pic->save();
      return $pic;
    }

    public function store(Request $request){
      $res = $request->json()->all();
      $pic = Picture::where('picture_id',$res['picture_id'])->first();
      if($pic){
        return $this->response->errorBadRequest("picture_id 已经存在");
      }
      $pic = new Picture();
      $pic = $this->_storePic($pic, $res);
      return $this->response->item($pic, new PictureTransformer());

    }

    public function update(Request $request){

    }

    public function delete($picture_id, Request $request){
      $pic = Picture::where('picture_id',$picture_id)->first();
      if($pic){
        $pic->delete();
      }
    }
}
