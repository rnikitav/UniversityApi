<?php

namespace App\Http\Requests\Tags;

use App\Rules\Helpers;

class Update extends Create
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'name' => Helpers::$filledString255,
        ]);
    }
}

