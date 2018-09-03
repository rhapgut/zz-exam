<?php

declare (strict_types = 1);

namespace App\Eloquent\Models;

class Role extends Base
{
    protected $table = 'roles';

    const NON_ADMIN_ROLE_NAME = 'Non Admin';
    const ADMIN_ROLE_NAME = 'Admin';

    protected $fillable = [
        'name',
    ];
}
