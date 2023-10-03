<?php

namespace App\Utils;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    public static function formatDate(?Carbon $date): ?string
    {
        return $date?->format('Y-m-d');
    }

    public static function formatDateTime(?Carbon $datetime): ?string
    {
        return $datetime?->format('Y-m-d H:i:s');
    }
}
