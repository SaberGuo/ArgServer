<?php

namespace App\Transformers;

use App\Models\Land;
use App\Models\Plot;
use App\Models\Specie;
use App\Models\Picture;
use League\Fractal\TransformerAbstract;

class DatasLandTransformer extends TransformerAbstract
{
    private function DealPictureAppId($pictures){
      foreach ($pictures as $pic) {
        $pic->owner_id = $pic->owner_app_id();
      }
      return $pictures;
    }
    public function transform(Land $land)
    {
      $plots = $land->plots()->get();
      foreach ($plots as $plot) {
        // code...
        $plot->species_list = $plot->species()->get();
        $plot->pictures_list = $this->DealPictureAppId($plot->pictures());
        $plot->land_id = $land->land_id;
        $plot->data = json_decode($plot->data);
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
        foreach ($plot->species_list as $species) {
          // code...
          $species->pictures_list = $this->DealPictureAppId($species->pictures());
          $species->plot_id = $plot->plot_id;
          $species->data = json_decode($species->data);
        }
      }
      $pics = $this->DealPictureAppId($land->pictures());

        return [
            'id' => $land->id,
            'land_id'=>$land->land_id,
            'code'=>$land->code,
            'upload_at'=>$land->upload_at,
            'lat' =>$land->lat,
            'lng' =>$land->lng,
            'alt' =>$land->alt,
            'type' =>$land->type,
            'investigator_name' =>$land->investigator_name,
            'investigated_at' =>$land->investigated_at,
            'plot_list'=>$plots,
            'pictures_list'=>$pics,
            //'uploaded_at' => $land->uploaded_at,
            'data' => json_decode($land->data),

        ];
    }
}
