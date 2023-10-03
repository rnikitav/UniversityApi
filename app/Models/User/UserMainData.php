<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $patronymic
 * @property string $email
 *
 * @property User $user
 */
class UserMainData extends Model
{
    use HasFactory;

    protected $table = 'user_main_data';

    protected $fillable = [
        'first_name',
        'last_name',
        'patronymic',
        'email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
