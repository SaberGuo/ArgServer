<?php

namespace App\Transformers;

use App\Models\Land;
use App\Models\Plot;
use App\Models\Specie;
use App\Models\Picture;
use League\Fractal\TransformerAbstract;

class DatasLandTransformer extends TransformerAbstract
{
    public function transform(Land $land)
    {
      $plots = $land->plots()->get();
      foreach ($plots as $plot) {
        // code...
        $plot->species_list = $plot->species()->get();
        $plot->pictures_list = $plot->pictures();
        foreach ($plot->species_list as $species) {
          // code...
          $species->pictures_list = $species->pictures();
        }
      }
      $pics = $land->pictures();
        return [
            'id' => $land->id,
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
