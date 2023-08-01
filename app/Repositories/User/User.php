<?php

namespace App\Repositories\User;

use App\Models\User\User as UserModel;
use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Collection;

class User extends AbstractRepository
{
    protected function getClassName(): string
    {
        return UserModel::class;
    }

    /**
     * Возвращает пользователей имеющих заданные разрешения
     * @param string $permission
     * @return Collection
     */
    public function withPermission(string $permission): Collection
    {
        return $this->model->permission($permission)->get();
    }

    public function byEmail(string $email): ?UserModel
    {
        return $this->model->where('email', $email)->get()->first();
    }

    public function byConfirmToken(string $token): ?UserModel
    {
        return $this->model->where('confirm_token', $token)->get()->first();
    }
}
