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
      $land = new Land();
      $res = $request->json()->all();
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
      if($res['investigator_name']){
        $land->investigator_name = $res['investigator_name'];
      }
      if($res['investigated_at']){
        $land->investigated_at = $res['investigated_at'];
      }
      if($res['data']){
        $land->data = json_encode($res['data']);
      }
      if($res['upload_at']){
        $land->upload_at = $res['upload_at'];
      }

      $land->user()->associate($this->user());
      $land->save();
      return $this->response->array($land);
    }

    public function storePlot($land_id, PlotRequest $request){
      $land = Land::where('land_id',$land_id);
      if($land && isOwner($this->user(), $land)){
        $res = $request->json()->all();
        $plot = new Plot();
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
        if($res['upload_at']){
          $plot->upload_at = $res['upload_at'];
        }

        foreach( $request->owner_list as $owner_id){
          $owner = Plot::where('plot_id', $owner_id)->first();
          if($owner){
            $plot->owners()->associate($owner);
          }
        }
        $plot->land()->associate($land);
        $plot->save();
        return $this->response->array($plot);
      }
      else{
        return $this->response->error("样地 ID 错误！");
      }


    }

    public function storeSpecie($land_id, $plot_id, SpecieRequest $request){
      $land = Land::where('land_id',$land_id);
      $plot = Plot::where('plot_id',$plot_id);
      if($plot && $land && isOwner($this->user(), $land) && isOwner($land, $plot)){
        $res = $request->json()->all();
        $specie = new Specie();
        if($res['specie_id']){
          $specie->specie_id = $res['specie_id'];
        }
        if($res['code']){
          $specie->code = $res['code'];
        }
        if($res['lat']){
          $specie->lat = $res['lat'];
        }
        if($res['lng']){
          $specie->lng = $res['lng'];
        }
        if($res['alt']){
          $specie->alt = $res['alt'];
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
          $specie->specie_id = json_encode($res['data']);
        }
        if($res['upload_at']){
          $specie->upload_at = $res['upload_at'];
        }

        $specie->plot()->associate($plot);
        $specie->save();
        return $this->response->array($specie);
      }
      else{
        return $this->response->error("样方 ID 错误！或权限不足");
      }
    }

    public function storePoint(Request $request){
      $res = $request->json()->all();

      $point = new Point();
      $point->code = json_encode($res['code']);
      $point->point_id=json_encode($res['point_id']);
      if($res['lat']){
      $point->lat=json_encode($res['lat']);
      }
      if($res['lng']){
      $point->lng=json_encode($res['lng']);
      }
      if($res['alt']){
      $point->alt=json_encode($res['alt']);
      }
      if($res['investigator_name']){
      $point->investigator_name=json_encode($res['investigator_name']);
      }
      if($res['investigated_at']){
      $point->investigated_at=json_encode($res['investigated_at']);
      }

      $point->data=json_encode($res['data']);
      if($res['upload_at']){
      $point->upload_at=json_encode($res['upload_at']);
      }

      $point->user()->associate($this->user());
      $point->save();
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
