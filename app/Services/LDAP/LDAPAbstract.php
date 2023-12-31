<?php

namespace App\Services\LDAP;

use App\Models\Permissions\Permission as PermissionModel;
use App\Models\User\User as UserModel;
use App\Repositories\User\User as UserRepository;
use Database\Factories\User\UserFactory;

class LDAPAbstract
{

    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function getOrCreateUser(array $data): UserModel
    {
        $userModel = $this->userRepository->byLogin($data['login']);

        if (!$userModel) {
            /** @var UserFactory $userFactory */
            $userFactory = UserModel::factory();

            /** @var UserModel $user */
            $userModel = $userFactory->external()->create(['login' => $data['login']]);
        }

        $userModel->givePermissionTo(PermissionModel::getPermissionStudent());

        return $userModel;
    }
}
