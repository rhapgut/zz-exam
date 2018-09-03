<?php

namespace Tests\Feature\Eloquent\Models;

use App\Eloquent\Models\Client;
use App\Eloquent\Models\User;
use App\Eloquent\Models\Role;
use App\Eloquent\Repositories\ClientRepository;
use App\Eloquent\Repositories\UserRepository;
use App\Eloquent\Repositories\RoleRepository;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /** @var ClientRepository */
    private $clientRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var RoleRepository */
    private $roleRepository;

    public function setUp()
    {
        parent::setUp();
        $this->clientRepository = $this->app->make(ClientRepository::class);
        $this->userRepository = $this->app->make(UserRepository::class);
        $this->roleRepository = $this->app->make(RoleRepository::class);
    }

    public function testUsers()
    {
        factory(Role::class)->create();
        /** @var Role $role */
        $role = $this->roleRepository->getFirst();
        factory(Client::class)->create();
        /** @var Client $client */
        $client = $this->clientRepository->getFirst();
        factory(User::class, 3)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getByClientId($client->id);
        $user->makeVisible([
            'id',
            'full_name'
        ]);
        $expected = $user->toArray();
        $client->users->makeVisible([
            'id',
            'full_name'
        ]);
        $actual = $client->users->toArray();
        array_multisort(array_column($expected, 'id'), SORT_ASC, $expected);
        array_multisort(array_column($actual, 'id'), SORT_ASC, $actual);
        $this->assertEquals($expected, $actual);
    }
}
