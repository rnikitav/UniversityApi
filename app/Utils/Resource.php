<?php

namespace App\Utils;

use Illuminate\Database\Eloquent\Model;

class Resource
{
    public static function fromFillable(Model $model): array
    {
        return array_reduce(
            $model->getFillable(),
            function (array $initial, string $field) use ($model) {
                $initial[$field] = $model?->{$field};
                return $initial;
            },
            []
        );
    }
}
