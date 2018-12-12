<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Base extends Model
{
    use SoftDeletes;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->hidden[] = 'deleted_at';
    }

    public function schema() {
        $schema = DB::select('desc '.$this->table);
        $schema = array_reduce($schema, function ($carry, $field) {
            $type = $field->Type;
            $type = explode('(', $type)[0];
            $field->Type = $type;
            foreach (['fillable', 'hidden'] as $key) {
                $field->$key = in_array($field->Field, $this->$key);
            }
            $carry[$field->Field] = $field;
            return $carry;
        }, []);
        return $schema;
    }

    /**
     * has relationship
     * @param  string  $model model name
     * @param  string  $id    object id
     * @return boolean
     */
    public function has($model, $id) {
        foreach ($this->$model as $instance) {
            if ($instance->id == $id) {
                return true;
            }
        }
        return false;
    }
}
