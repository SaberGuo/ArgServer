<?php

namespace App\Transformers;

use App\Models\Picture;
use League\Fractal\TransformerAbstract;

class PictureTransformer extends TransformerAbstract
{
    public function transform(Picture $pic)
    {
      return [
          'id' => $pic->id,
          'type' =>$pic->type,
          'owner_id' =>$pic->owner_app_id(),
          'url' =>$pic->url
      ];
    }
}
