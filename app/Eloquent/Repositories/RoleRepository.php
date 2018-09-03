<?php

declare (strict_types = 1);

namespace App\Eloquent\Repositories;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Eloquent\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct()
    {
        $this->setModel(new Role());
    }
}
