<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

use App\Models\Land;
use App\Models\Plot;
use App\Models\Point;
use App\Models\Specie;
use App\Models\Picture;

use App\Transformers\DevicesTransformer;
use App\Transformers\DeviceDataTransformer;
use App\Http\Requests\Api\LandRequest;
use App\Http\Requests\Api\PlotRequest;
use App\Http\Requests\Api\SpecieRequest;
use App\Http\Requests\Api\PointRequest;
use App\Http\Requests\Api\PictureRequst;

class DatasController extends Controller
{
    //
    public function store(Request $request){

    }

    public function updateLand($land_id, LandRequest $request){
      $land = Land::where('land_id',$land_id);
      if($land->first() && isOwner($this->user(), $land->first())){
        $land->update($request);
        return $this->response->item($land->first());
      }
      return $this->response->error("更新错误!");


    }

    public function updatePlot($land_id, $plot_id, PlotRequest $request){
      $land = Land::where('land_id',$land_id)->first();
      $plot = Plot::where('plot_id',$plot_id)->first();
      if($land && isOwner($this->user()) && $plot && isOwner($land, $plot)){
        $plot->update($request);
        $plot->owners()->detach();
        foreach( $request->owner_list as $owner_id){
          $owner = Plot::where('plot_id', $owner_id)->first();
          if($owner){
            $plot->owners()->associate($owner);
          }
        }
        return $this->response->item($plot);
      }
      return $this->response->error("更新错误!");
    }

    public function updatePoint($point_id, PointRequest $request){
      $point = Point::where('point_id', $point_id)->first();
      if($point && isOwner($this->user())){
        $point->update($request);
        return $this->response->item($point);
      }
      return $this->response->error("更新错误!");
    }

    public function updateSpecie($land_id, $plot_id, $specie_id, SpecieRequest $request){
      $land = Land::where('land_id', $land_id)->first();
      $plot = Plot::where('plot_id', $plot_id)->first();
      $specie = Specie::where('specie_id', $specie_id)->first();

      if($land && isOwner($this->user(), $land) && $plot && isOwner($land, $plot) && $specie && isOwner($plot, $specie)){
        $specie->update($request);
        return $this->response->item($specie);
      }
      return $this->response->error("更新错误!");
    }

    public function storeLand(LandRequest $request){
      $land = Land::Create([
        'land_id'=>$request->land_id,
        'code'=>$request->code,
        'lat'=>$request->lat,
        'lng'=>$request->lng,
        'alt'=>$request->alt,
        'type'=>$request->type,
        'investigator_name'=>$request->investigator_name,
        'investigated_at'=>$request->investigated_at,
        'data'=>$request->data,
        'uploaded_at'=>$request->uploaded_at
      ]);
      $land->user()->associate($this->user());

      return $this->response->array($land);
    }

    public function storePlot($land_id, PlotRequest $request){
      $land = Land:where('land_id',$land_id);
      if($land && isOwner($this->user(), $land)){
        $plot = Plot::Create([
          'plot_id'=>$request->plot_id,
          'code'=>$request->code,
          'lat'=>$request->lat,
          'lng'=>$request->lng,
          'alt'=>$request->alt,
          'type'=>$request->type,
          'investigator_name'=>$request->investigator_name,
          'investigated_at'=>$request->investigated_at,
          'data'=>$request->data,
          'uploaded_at'=>$request->uploaded_at
        ]);
        foreach( $request->owner_list as $owner_id){
          $owner = Plot::where('plot_id', $owner_id)->first();
          if($owner){
            $plot->owners()->associate($owner);
          }
        }
        $plot->land()->associate($land);
        return $this->response->array($plot);
      }
      else{
        return $this->response->error("样地 ID 错误！");
      }


    }

    public function storeSpecie($land_id, $plot_id, SpecieRequest $request){
      $land = Land::where('land_id',$land_id);
      $plot = Plot:where('plot_id',$plot_id);
      if($plot && $land && isOwner($this->user(), $land) && isOwner($land, $plot)){
        $specie = Specie::Create([
          'specie_id'=>$request->specie_id,
          'code'=>$request->code,
          'lat'=>$request->lat,
          'lng'=>$request->lng,
          'alt'=>$request->alt,
          'type'=>$request->type,
          'name'=>$request->name,
          'latin_name'=>$request->latin_name,
          'data'=>$request->data,
          'uploaded_at'=>$request->uploaded_at
        ]);
        $specie->plot()->associate($plot);
        return $this->response->array($specie);
      }
      else{
        return $this->response->error("样方 ID 错误！或权限不足");
      }
    }

    public function storePoint(PointRequest $request){
      $point = Point::Create([
        'point_id'=>$request->point_id,
        'lat'=>$request->lat,
        'lng'=>$request->lng,
        'alt'=>$request->alt,
        'investigator_name'=>$request->investigator_name,
        'investigated_at'=>$request->investigated_at,
        'data'=>$request->data,
        'uploaded_at'=>$request->uploaded_at
      ]);
      $point->user()->associate($this->user());
      return $this->response->array($point);
    }

    public function storePicture(PictureRequst $request){

    }

    function isOwner($owner, $model){
      $ot = call_user_func(array($model, $owner->type))->get();
      return $ot->id == $owner->id;
    }

    public function showLand($land_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      if(!$land){
        return $this->response->error("样地 ID 错误！");
      }
      $owner = $land->user()->get();
      if($owner->id == $this->user()->id){
        return $item->response->item($land, new DatasLandTransformer());
      }
      else{
        return $this->response->error("权限不足！");
      }
    }

    public function showPlot($land_id, $plot_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      $plot = Plot::where('plot_id',$plot_id)->first();
      if($land && isOwner($this->user(), $land) && $plot && isOwner($land, $plot)){
        return $item->response->item($plot, new DatasPlotTransformer());
      }
      else{
        return $this->response->error("权限不足！");
      }
    }

    public function showSpecie($land_id, $plot_id, $specie_id, Request $request){
      $land = Land::where('land_id',$land_id)->first();
      $plot = Plot::where('plot_id',$plot_id)->first();
      $specie = Specie::where('specie_id',$specie_id)->first();
      if($land && isOwner($this->user(), $land) && $plot && isOwner($land, $plot) && $specie && isOwner($plot, $specie)){
        return $item->response->item($specie, new DatasSpecieTransformer());
      }
      else{
        return $this->response->error("权限不足！");
      }
    }

    public function showPoint($point_id, Request $request){
      $point = Point::where('point_id',$point_id)->first();
      if($point && isOwner($this->user(), $point)){
        return $item->response->item($point, new DatasPointTransformer());
      }
      else{
        return $this->response->error("权限不足！");
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
