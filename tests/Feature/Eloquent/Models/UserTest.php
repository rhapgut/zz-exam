<?php

declare (strict_types = 1);

namespace Test\Feature\Eloquent\Models;

use App\Eloquent\Models\Client;
use App\Eloquent\Models\Role;
use App\Eloquent\Models\User;
use App\Eloquent\Models\UserPhoneNumber;
use App\Eloquent\Repositories\ClientRepository;
use App\Eloquent\Repositories\RoleRepository;
use App\Eloquent\Repositories\UserPhoneNumberRepository;
use App\Eloquent\Repositories\UserRepository;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @var UserRepository */
    private $userRepository;

    /** @var RoleRepository */
    private $roleRepository;

    /** @var ClientRepository */
    private $clientRepository;

    /** @var UserPhoneNumberRepository */
    private $phoneNumberRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = $this->app->make(UserRepository::class);
        $this->roleRepository = $this->app->make(RoleRepository::class);
        $this->clientRepository = $this->app->make(ClientRepository::class);
        $this->phoneNumberRepository = $this->app->make(UserPhoneNumberRepository::class);
    }

    public function testRelationship()
    {
        factory(Role::class)->create();
        /** @var Role $role */
        $role = $this->roleRepository->getFirst();
        factory(Client::class)->create();
        /** @var Client $client */
        $client = $this->clientRepository->getFirst();
        factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst();
        factory(UserPhoneNumber::class, 3)->create([
            'user_id' => $user->id
        ]);
        /** @var UserPhoneNumber $phoneNumbers */
        $phoneNumbers = $this->phoneNumberRepository->getAll();
        array_multisort(array_column($phoneNumbers->toArray(), 'number'), SORT_ASC, $phoneNumbers->toArray());
        array_multisort(array_column($user->phoneNumbers->toArray(), 'number'), SORT_ASC, $user->phoneNumbers->toArray());
        $this->assertEquals($role->toArray(), $user->role->toArray());
        $this->assertEquals($client->toArray(), $user->client->toArray());
        $this->assertEquals($phoneNumbers->toArray(), $user->phoneNumbers->toArray());
    }
}
