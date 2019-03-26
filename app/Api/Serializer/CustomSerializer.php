<?php
/**
 * Created by PhpStorm.
 * User: ning
 * Date: 2017/5/2
 * Time: 下午5:21
 */

namespace App\Api\Serializer;


use League\Fractal\Serializer\ArraySerializer;

class CustomSerializer extends ArraySerializer
{
    public function collection($resourceKey, array $data)

    {
        return $data;
    }
    public function item($resourceKey, array $data)

    {
        return $data;
    }
}
