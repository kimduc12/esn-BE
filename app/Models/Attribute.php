<?php

namespace App\Models;
use Rinvex\Attributes\Models\Attribute as AttributeModel;

class Attribute extends AttributeModel
{
    public function varcharValues()
    {
        return $this->hasMany(AttributeVarcharValue::class, 'attribute_id', 'id');
    }
}
