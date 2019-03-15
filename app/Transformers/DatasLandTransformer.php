<?php

namespace App\Transformers;

use App\Models\Land;
use League\Fractal\TransformerAbstract;

class DatasLandTransformer extends TransformerAbstract
{
    public function transform(Land $land)
    {
        return [
            'id' => $land->id,
            'lat' =>$land->lat,
            'lng' =>$land->lng,
            'alt' =>$land->alt,
            'type' =>$land->type,
            'investigator_name' =>$land->investigator_name,
            'investigated_at' =>$land->investigated_at,
            //'uploaded_at' => $land->uploaded_at,
            'data' => json_decode($land->data),

        ];
    }
}
