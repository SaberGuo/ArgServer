<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Models\Land;
use App\Models\Plot;
use App\Models\Point;
use App\Models\Specie;
use App\Models\Picture;
use Illuminate\Support\Facades\Log;
use App\Transformers\DevicesTransformer;
use App\Transformers\DeviceDataTransformer;
use App\Http\Requests\Api\LandRequest;
use App\Http\Requests\Api\PlotRequest;
use App\Http\Requests\Api\SpecieRequest;
use App\Http\Requests\Api\PointRequest;
use App\Http\Requests\Api\PictureRequst;

use App\Transformers\DatasPointTransformer;
use App\Transformers\DatasPlotTransformer;
use App\Transformers\DatasSpecieTransformer;
use App\Transformers\UserTransformer;
use App\Transformers\DatasLandTransformer;

class DatasController extends Controller
{
    //
    public function store(Request $request){

    }

    public function updateLand($land_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      if($land && $this->isOwner($this->user(), $land)){
        $res = $request->json()->all();
        $land = $this->_storeLand($land, $res);

        return $this->response->item($land, new DatasLandTransformer());
      }
      return $this->response->errorUnauthorized("更新错误!");


    }

    public function updatePlot($land_id, $plot_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      $plot = Plot::where('plot_id',$plot_id)->first();


      if($land && $this->isOwner($this->user()) && $plot && $this->isOwner($land, $plot)){
        $res = $request->json()->all();
        $plot = $plot->_storePlot($land,$plot,$res);
        $plot->owners()->detach();
        foreach( $request->owner_list as $owner_id){
          $owner = Plot::where('plot_id', $owner_id)->first();
          if($owner){
            $plot->owners()->associate($owner);
          }
        }
        return $this->response->item($plot, new DatasPlotTransformer());
      }
      return $this->response->errorUnauthorized("更新错误!");
    }

    public function updatePoint($point_id, Request $request){
      $point = Point::where('point_id', $point_id)->first();
      if($point && $this->isOwner($this->user(),$point)){
        $res = $request->json()->all();

        $point = $this->_storePoint($point,$res);
        return $this->response->item($point, new DatasPointTransformer());
      }
      return $this->response->errorUnauthorized("更新错误!");
    }

    public function updateSpecie($land_id, $plot_id, $species_id, Request $request){
      $land = Land::where('land_id', $land_id)->first();
      $plot = Plot::where('plot_id', $plot_id)->first();
      $specie = Specie::where('species_id', $species_id)->first();

      if($land && $this->isOwner($this->user(), $land) && $plot && $this->isOwner($land, $plot) && $specie && $this->isOwner($plot, $specie)){
        //$specie->update($request);
        $res = $request->json()->all();
        $specie = $this->_storeSpecie($plot, $specie, $res);
        return $this->response->item($specie, new DatasSpecieTransformer());
      }
      return $this->response->errorUnauthorized("更新错误!");
    }
    public function storeLand(Request $request){
      $res = $request->json()->all();
      $land = Land::where('land_id',$res['land_id'])->first();
      if($land){
        return $this->response->errorBadRequest("land_id 已经存在");
      }
      $land = new Land();

      $land = $this->_storeLand($land, $res);
      return $this->response->item($land, new DatasLandTransformer());

    }

    public function _storeLand($land, $res){


      if($res['land_id']){
        $land->land_id = $res['land_id'];
      }
      if($res['code']){
        $land->code = $res['code'];
      }
      if($res['lat']){
        $land->lat = $res['lat'];
      }
      if($res['lng']){
        $land->lng = $res['lng'];
      }
      if($res['alt']){
        $land->alt = $res['alt'];
      }
      if($res['type']){
        $land->type = $res['type'];
      }

      if($res['data']){
        $land->data = json_encode($res['data']);
      }
      if($res['upload_at']){
        $land->upload_at = $res['upload_at'];
      }
      $land->user()->associate($this->user());
      $land->save();

      if(array_key_exists('plot_list', $res) && $res['plot_list']){
        foreach ($res['plot_list'] as $p) {
          $plot = new Plot();
          $this->_storePlot($land, $plot, $p);
        }
      }
      return $land;
    }
    protected function _storeSpecie($plot, $specie, $res){
      //$specie = new Specie();
      if($res['species_id']){
        $specie->species_id = $res['species_id'];
      }
      if($res['code']){
        $specie->code = $res['code'];
      }

      if($res['type']){
        $specie->type = $res['type'];
      }
      if($res['name']){
        $specie->name = $res['name'];
      }
      if($res['latin_name']){
        $specie->latin_name = $res['latin_name'];
      }
      if($res['data']){
        $specie->data = json_encode($res['data']);
      }
      if($res['upload_at']){
        $specie->upload_at = $res['upload_at'];
      }

      $specie->plot()->associate($plot);
      $specie->save();
      return $specie;
    }
    protected function _storePlot($land, $plot, $res){
      //$plot = new Plot();
      if($res['plot_id']){
        $plot->plot_id = $res['plot_id'];
      }
      if($res['code']){
        $plot->code = $res['code'];
      }
      if($res['lat']){
        $plot->lat = $res['lat'];
      }
      if($res['lng']){
        $plot->lng = $res['lng'];
      }
      if($res['alt']){
        $plot->alt = $res['alt'];
      }
      if($res['type']){
        $plot->type = $res['type'];
      }
      if($res['investigator_name']){
        $plot->investigator_name = $res['investigator_name'];
      }
      if($res['investigated_at']){
        $plot->investigated_at = $res['investigated_at'];
      }
      if($res['data']){
        $plot->data = json_encode($res['data']);
      }
      if(array_key_exists('upload_at', $res) && $res['upload_at']){
        $plot->upload_at = $res['upload_at'];
      }

      if(array_key_exists('owner_list', $res) && $res['owner_list']){
        foreach( $res['owner_list'] as $owner_id){
          $owner = Plot::where('plot_id', $owner_id)->first();
          if($owner){
            $plot->owners()->associate($owner);
          }
        }
      }

      $plot->land()->associate($land);
      $plot->save();

      if(array_key_exists('species_list', $res) && $res['species_list']){
        foreach ($res['species_list'] as $p) {

          $specie = new Specie();
          $this->_storeSpecie($plot,$specie,$p);
        }
      }

      return $plot;
    }
    public function storePlot($land_id, Request $request){
      $res = $request->json()->all();
      $plot = Plot::where('plot_id',$res['plot_id'])->first();
      if($plot){
        return $this->response->errorBadRequest("plot_id 已经存在");
      }
      $land = Land::where('land_id',$land_id);
      if($land && $this->isOwner($this->user(), $land)){

        $plot = new Plot();
        $plot = $this->_storePlot($land,$plot, $res);

        return $this->response->item($plot, new DatasPlotTransformer());
      }
      else{
        return $this->response->errorUnauthorized("样地 ID 错误！");
      }
    }

    public function storeSpecie($land_id, $plot_id, Request $request){
      $res = $request->json()->all();
      $specie = Specie::where('species_id',$res['species_id'])->first();
      if($specie){
        return $this->response->errorBadRequest("species_id 已经存在");
      }

      $land = Land::where('land_id',$land_id);
      $plot = Plot::where('plot_id',$plot_id);
      if($plot && $land && $this->isOwner($this->user(), $land) && $this->isOwner($land, $plot)){
        $specie = new Specie();
        $specie = $this->_storeSpecie($plot, $specie ,$res);
        return $this->response->array($specie);
      }
      else{
        return $this->response->errorUnauthorized("样方 ID 错误！或权限不足");
      }
    }
    public function storePoint(Request $request){
      $res = $request->json()->all();
      $point = Point::where('point_id',$res['point_id'])->first();
      if($point){
        return $this->response->errorBadRequest("point_id 已经存在");
      }
      $point = new Point();
      $point = $this->_storePoint($point, $res);
      return $this->response->item($point, new DatasPointTransformer());

    }
    public function _storePoint($point, $res){

      $point->code = $res['code'];
      $point->point_id=$res['point_id'];
      if($res['lat']){
      $point->lat=$res['lat'];
      }
      if($res['lng']){
      $point->lng=($res['lng']);
      }
      if($res['alt']){
      $point->alt=($res['alt']);
      }
      if($res['investigator_name']){
      $point->investigator_name=($res['investigator_name']);
      }
      if($res['investigated_at']){
      $point->investigated_at=($res['investigated_at']);
      }

      $point->data=json_encode($res['data']);
      if($res['upload_at']){
      $point->upload_at=($res['upload_at']);
      }

      $point->user()->associate($this->user());
      $point->save();
      return $point;
    }

    public function storePicture(PictureRequst $request){

    }

    function isOwner($owner, $model){
      $ot = call_user_func(array($model, $owner::$tp))->first();
      return $ot->id == $owner->id;
    }

    public function showLand($land_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      if(!$land){
        return $this->response->errorUnauthorized("样地 ID 错误！");
      }
      $owner = $land->user()->get();
      if($owner->id == $this->user()->id){
        return $item->response->item($land, new DatasLandTransformer());
      }
      else{
        return $this->response->errorUnauthorized("权限不足！");
      }
    }

    public function showPlot($land_id, $plot_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      $plot = Plot::where('plot_id',$plot_id)->first();
      if($land && $this->isOwner($this->user(), $land) && $plot && $this->isOwner($land,$plot)){
        return $item->response->item($plot, new DatasPlotTransformer());
      }
      else{
        return $this->response->errorUnauthorized("权限不足！");
      }
    }

    public function showSpecie($land_id, $plot_id, $species_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      $plot = Plot::where('plot_id',$plot_id)->first();
      $specie = Specie::where('species_id',$species_id)->first();
      if($land && $this->isOwner($this->user(),$land) && $plot && $this->isOwner($land,$plot) && $specie && $this->isOwner($plot,$specie)){
        return $item->response->item($specie, new DatasSpecieTransformer());
      }
      else{
        return $this->response->errorUnauthorized("权限不足！");
      }
    }

    public function showPoint($point_id, Request $request){
      $point = Point::where('point_id',$point_id)->first();
      if($point && $this->isOwner($this->user(),$point)){
        return $this->response->item($point, new DatasPointTransformer());
      }
      else{
        return $this->response->errorUnauthorized("权限不足！");
      }
    }

    public function deletePlot($land_id, $plot_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      $plot = Plot::where('plot_id',$plot_id)->first();
      if($land && $this->isOwner($this->user(),$land) && $plot && $this->isOwner($land,$plot)){
        $plot->delete();
      }
      else{
        return $this->response->errorUnauthorized("权限不足！");
      }
    }
    public function deleteLand($land_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      Log::info($land);
      Log::info($this->isOwner($this->user(),$land));
      if($land && $this->isOwner($this->user(),$land)){
        $land->delete();
      }
      else{
        return $this->response->errorUnauthorized("权限不足！");
      }
    }

    public function deleteSpecie($land_id, $plot_id, $species_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      $plot = Plot::where('plot_id',$plot_id)->first();
      $species = Specie::where('species_id', $species_id)->first();
      if($land && $this->isOwner($this->user(),$land) && $plot && $this->isOwner($land,$plot) && $species && $this->isOwner($plot,$species)){
        $species->delete();
      }
      else{
        return $this->response->errorUnauthorized("权限不足！");
      }
    }

    public function deletePoint($point_id, Request $request){
      $point = Point::where('point_id',$point_id)->first();
      if($point && $this->isOwner($this->user(),$point)){
        $point->delete();
      }
      else{
        return $this->response->errorUnauthorized("权限不足！");
      }

    }

    public function indexLand(Request $request){

    }
    public function indexPlot(Request $request){

    }

    public function indexSpecie(Request $request){

    }

    public function indexPoint(Request $request){

    }



}
