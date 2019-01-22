<?php

namespace App\Transformers;

use App\Models\Plot;
use League\Fractal\TransformerAbstract;

class DatasPlotTransformer extends TransformerAbstract
{
    public function transform(Plot $plot)
    {
      return [
          'id' => $plot->id,
          'lat' =>$plot->lat,
          'lng' =>$plot->lng,
          'alt' =>$plot->alt,
          'type' =>$plot->type,
          'investigator_name' =>$plot->investigator_name,
          'investigated_at' =>$plot->investigated_at,
          //'uploaded_at' => $land->uploaded_at,
          'data' => $plot->data,

      ];
    }
}
