<?php

declare(strict_types=1);

namespace Test\Feature\Eloquent\Models;

use App\Eloquent\Models\Role;
use App\Eloquent\Repositories\RoleRepository;
use Tests\TestCase;

class RoleTest extends TestCase
{
    /** @var RoleRepository */
    private $roleRepository;

    public function setUp()
    {
        parent::setUp();
        $this->roleRepository = $this->app->make(RoleRepository::class);
    }
}
