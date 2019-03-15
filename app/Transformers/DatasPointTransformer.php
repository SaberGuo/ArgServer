<?php

namespace App\Transformers;

use App\Models\Point;
use League\Fractal\TransformerAbstract;

class DatasPointTransformer extends TransformerAbstract
{
    public function transform(Point $point)
    {
      return [
          'point_id' => $point->point_id,
          'lat' =>$point->lat,
          'lng' =>$point->lng,
          'alt' =>$point->alt,

          'investigator_name' =>$point->investigator_name,
          'investigated_at' =>$point->investigated_at,
          //'uploaded_at' => $land->uploaded_at,
          'data' => json_decode($point->data),

      ];
    }
}
