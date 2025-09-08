<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface extends MasterRepositoryInterface
{
    public function createWithRoles(array $data, string $roleName);
    public function resetPassword(int $id, array $data);
}
