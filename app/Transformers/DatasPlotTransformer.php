<?php

namespace App\Transformers;

use App\Models\Plot;
use League\Fractal\TransformerAbstract;

class DatasPlotTransformer extends TransformerAbstract
{
    public function transform(Plot $plot)
    {
      $owner_list = array('plot'=>array());
      $owners = $plot->owners()->get();
      foreach ($owners as $ow) {
        // code...
        if(array_key_exists($ow::$tp, $owner_list)){
          array_push($owner_list[$ow::$tp],$ow[$ow::$tp."_id"]);
        }
        else{
          $owner_list[$ow::$tp] = array($ow[$ow::$tp."_id"]);
        }
      }
      $plot->owner_list = $owner_list;
      return [
          'id' => $plot->id,
          'plot_id'=>$plot->plot_id,
          'code'=>$plot->code,
          'lat' =>$plot->lat,
          'lng' =>$plot->lng,
          'alt' =>$plot->alt,
          'type' =>$plot->type,
          'investigator_name' =>$plot->investigator_name,
          'investigated_at' =>$plot->investigated_at,
          'owner_list'=>$plot->owner_list,
          'upload_at' => $plot->upload_at,
          'data' => json_decode($plot->data),

      ];
    }
}
