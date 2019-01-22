<?php

namespace App\Transformers;

use App\Models\Point;
use League\Fractal\TransformerAbstract;

class DatasPointTransformer extends TransformerAbstract
{
    public function transform(Point $point)
    {
      return [
          'id' => $plot->id,
          'lat' =>$plot->lat,
          'lng' =>$plot->lng,
          'alt' =>$plot->alt,

          'investigator_name' =>$plot->investigator_name,
          'investigated_at' =>$plot->investigated_at,
          //'uploaded_at' => $land->uploaded_at,
          'data' => $plot->data,

      ];
    }
}
