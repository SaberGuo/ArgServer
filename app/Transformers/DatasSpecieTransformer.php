<?php

namespace App\Transformers;

use App\Models\Specie;
use League\Fractal\TransformerAbstract;

class DatasSpecieTransformer extends TransformerAbstract
{
    public function transform(Specie $specie)
    {
      return [
          'id' => $specie->id,
          'type' =>$specie->type,
          'name' =>$specie->name,
          'latin_name' =>$specie->latin_name,
          'code' =>$specie->code,
          'uploaded_at' => $specie->uploaded_at,
          'data' => json_decode($specie->data),
      ];
    }
}
