<?php

namespace App\Transformers;

use App\Models\Specie;
use League\Fractal\TransformerAbstract;

class DatasSpecieTransformer extends TransformerAbstract
{
    public function transform(Specie $specie)
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
          'data' => json_decode($plot->data),
      ];
    }
}
