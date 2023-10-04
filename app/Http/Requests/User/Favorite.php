<?php

namespace App\Http\Requests\User;

use App\Models\Accelerator\Case\AcceleratorCase;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $type
 * @property int $id
 */
class Favorite extends FormRequest
{
    protected $stopOnFirstFailure = true;

    protected static array $allowedTypes = [
        'case' => AcceleratorCase::class
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:' . implode(',', array_keys(static::$allowedTypes)),
            'id' => sprintf('required|integer|exists:%s,id', static::$allowedTypes[$this->type] ?? ''),
        ];
    }
}

