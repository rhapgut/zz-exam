<?php

declare(strict_types=1);

namespace Test\Feature\Eloquent\Repositories;

use App\Eloquent\Repositories\RoleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @var RoleRepository */
    private $roleRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->roleRepository = $this->app->make(RoleRepository::class);
    }
}
