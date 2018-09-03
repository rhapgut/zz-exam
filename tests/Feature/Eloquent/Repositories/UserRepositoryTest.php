<?php

declare(strict_types=1);

namespace Test\Feature\Eloquent\Repositories;

use App\Eloquent\Models\Client;
use App\Eloquent\Models\User;
use App\Eloquent\Models\Role;
use App\Eloquent\Repositories\ClientRepository;
use App\Eloquent\Repositories\UserRepository;
use App\Eloquent\Repositories\RoleRepository;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    /** @var ClientRepository */
    private $clientRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var RoleRepository */
    private $roleRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->clientRepository = $this->app->make(ClientRepository::class);
        $this->userRepository = $this->app->make(UserRepository::class);
        $this->roleRepository = $this->app->make(RoleRepository::class);
    }

    public function testGetByClientId()
    {
        factory(Role::class)->create();
        /** @var Role $role */
        $role = $this->roleRepository->getFirst();
        factory(Client::class)->create();
        /** @var Client $client */
        $client = $this->clientRepository->getFirst();
        $userOne = factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ])->makeVisible([
            'id',
            'full_name',
            'client_id'
        ]);
        $userTwo = factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ])->makeVisible([
            'id',
            'full_name',
            'client_id'
        ]);
        $userThree = factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ])->makeVisible([
            'id',
            'full_name',
            'client_id'
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getByClientId($client->id);
        $user->makeVisible([
            'id',
            'full_name',
            'client_id'
        ]);
        $expected = [
            $userOne->toArray(),
            $userTwo->toArray(),
            $userThree->toArray()
        ];
        $actual = $user->toArray();
        array_multisort(array_column($expected, 'id'), SORT_ASC, $expected);
        array_multisort(array_column($actual, 'id'), SORT_ASC, $actual);
        $this->assertEquals($expected[0]['client_id'], $actual[0]['client_id']);
        $this->assertEquals($expected[0]['email'], $actual[0]['email']);
        $this->assertEquals($expected[0]['full_name'], $actual[0]['full_name']);
        $this->assertEquals($expected[1]['client_id'], $actual[1]['client_id']);
        $this->assertEquals($expected[1]['email'], $actual[1]['email']);
        $this->assertEquals($expected[1]['full_name'], $actual[1]['full_name']);
        $this->assertEquals($expected[2]['client_id'], $actual[2]['client_id']);
        $this->assertEquals($expected[2]['email'], $actual[2]['email']);
        $this->assertEquals($expected[2]['full_name'], $actual[2]['full_name']);
    }

    public function testCreateAndReturnUser()
    {
        factory(Role::class)->create();
        /** @var Role $role */
        $role = $this->roleRepository->getFirst();
        factory(Client::class)->create();
        /** @var Client $client */
        $client = $this->clientRepository->getFirst();
        $data = [
            'role_id' => $role->id,
            'client_id' => $client->id,
            'email' => 'test_user@email.com',
            'full_name' => 'Test User',
            'password' => 'dfgdfgwef'
        ];
        $user = $this->userRepository->createAndReturnuser($data);
        $user->makeVisible([
            'full_name',
            'client_id',
            'role_id'
        ]);
        $this->assertEquals($role->id, $user->role_id);
        $this->assertEquals($client->id, $user->client_id);
        $this->assertEquals('test_user@email.com', $user->email);
        $this->assertEquals('Test User', $user->full_name);
    }

    public function testUpdateUser()
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
        $dataToUpdate = [
            'full_name' => 'test update'
        ];
        $this->userRepository->updateUser($user, $dataToUpdate);
        $this->assertEquals('test update', $user->full_name);
    }

    public function testUpdateAndReturnUser()
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
        $dataToUpdate = [
            'full_name' => 'test update'
        ];
        $updatedUser = $this->userRepository->updateAndReturnUser($user, $dataToUpdate);
        $this->assertEquals('test update', $updatedUser->full_name);
    }

    public function testDeleteUser()
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
            'email' => 'delete_user@email.com'
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst();
        $this->userRepository->deleteUser($user);
        $record = [
            'email' => 'delete_user@email.com'
        ];
        $this->assertDatabaseMissing('users', $record);
    }
}
