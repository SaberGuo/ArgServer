<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Picture;
use App\Transformers\PictureTransformer;

class PictureController extends Controller
{
    //
    public function index(Request $request){

    }

    public function show($pic_id, Request $request){

    }

    public function _storePic($pic, $res){
      if($res['type']){
        $pic->type = $res['type'];
      }
      if($res['owner_id']){
        $pic->owner_id = $res['owner_id'];
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

    public function delete(Request $request){

    }
}
