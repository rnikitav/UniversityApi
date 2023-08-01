<?php

namespace App\Utils;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
class Helpers
{
    public static function keysRules(Request $request, $method = 'rules'): array
    {
        $rules = Container::getInstance()->call([$request, $method]);
        return Arr::where(array_keys($rules), function ($value) {
            return !str_contains($value, '*');
        });
    }
}
