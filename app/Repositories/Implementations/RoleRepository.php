<?php

namespace App\Repositories\Implementations;

use App\Models\Role;

class RoleRepository
{
    public function all()
    {
        return Role::all();
    }

    public function findByName(string $name)
    {
        return Role::where('name', $name)->get();
    }
}
