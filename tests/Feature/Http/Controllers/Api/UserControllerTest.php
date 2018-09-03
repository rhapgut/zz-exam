<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Eloquent\Models\Client;
use App\Eloquent\Models\Role;
use App\Eloquent\Models\User;
use App\Eloquent\Repositories\UserRepository;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use WithoutMiddleware;

    /** @var string */
    private $controllerName;

    /** @var UserRepository */
    private $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->controllerName = 'Api\UserController';
        $this->userRepository = $this->app->make(UserRepository::class);
    }

    public function testGetMyself()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst();
        $path = action(sprintf('%s@getMyself', $this->controllerName));
        $data = ['user' => $user];
        $response = $this->call('GET', $path, $data);
        $response->assertSuccessful();
        $actual = $response->getOriginalContent();
        $this->assertEquals($user->email, $actual->email);
        $this->assertEquals($user->full_name, $actual->full_name);
    }

    public function testGetUserNoAccess()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::NON_ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class, 2)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $requestUser */
        $requestUser = $this->userRepository->getFirst('id', 'asc');
        /**  @var User $user */
        $user = $this->userRepository->getFirst('id', 'desc');
        $path = action(sprintf('%s@getUser', $this->controllerName), $user->id);
        $data = ['user' => $requestUser];
        $response = $this->call('GET', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 403,
            'message' => [
                'errors' => [
                    'Your access to this user is forbidden.'
                ]
            ]
        ];
        $response->assertStatus(403)
            ->assertJson($expected);
    }

    public function testGetUserNotFound()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $requestUser */
        $requestUser = $this->userRepository->getFirst();
        $userId = -1;
        $path = action(sprintf('%s@getUser', $this->controllerName), $userId);
        $data = ['user' => $requestUser];
        $response = $this->call('GET', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 422,
            'message' => [
                'errors' => [
                    'The specified user not found.'
                ]
            ]
        ];
        $response->assertStatus(422)
            ->assertJson($expected);
    }

    public function testGetUserSuccessful()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class, 2)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $requestUser */
        $requestUser = $this->userRepository->getFirst('id', 'asc');
        /**  @var User $user */
        $user = $this->userRepository->getFirst('id', 'desc');
        $path = action(sprintf('%s@getUser', $this->controllerName), $user->id);
        $data = ['user' => $requestUser];
        $response = $this->call('GET', $path, $data);
        $response->assertSuccessful();
        $actual = $response->getOriginalContent();
        $this->assertEquals($user->email, $actual->email);
        $this->assertEquals($user->full_name, $actual->full_name);
    }

    public function testCreateUserNoAccess()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::NON_ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst();
        $path = action(sprintf('%s@store', $this->controllerName));
        $data = ['user' => $user];
        $response = $this->call('POST', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 403,
            'message' => [
                'errors' => [
                    'Your access to this user is forbidden.'
                ]
            ]
        ];
        $response->assertStatus(403)
            ->assertJson($expected);
    }

    public function testCreateUserValidationFail()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst();
        $path = action(sprintf('%s@store', $this->controllerName));
        $data = ['user' => $user];
        $response = $this->call('POST', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 422,
            'message' => [
                'errors' => [
                    'The email field is required.',
                    'The password field is required.',
                    'The full name field is required.',
                    'The role id field is required.',
                ]
            ]
        ];
        $response->assertStatus(422)
            ->assertJson($expected);
    }

    public function testCreateUserSuccessful()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst();
        $path = action(sprintf('%s@store', $this->controllerName));
        $data = [
            'user' => $user,
            'email' => 'new@email.com',
            'password' => 'dfdofgidf',
            'full_name' => 'new user',
            'role_id' => $role->id
        ];
        $response = $this->call('POST', $path, $data);
        $response->assertSuccessful();
        $record = [
            'email' => 'new@email.com',
            'full_name' => 'new user',
            'role_id' => $role->id
        ];
        $this->assertDatabaseHas('users', $record);
    }

    public function testUpdateUserInfoNoAccess()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::NON_ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst();
        $userId = -1;
        $path = action(sprintf('%s@updateUserInfo', $this->controllerName), $userId);
        $data = ['user' => $user];
        $response = $this->call('PATCH', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 403,
            'message' => [
                'errors' => [
                    'Your access to this user is forbidden.'
                ]
            ]
        ];
        $response->assertStatus(403)
            ->assertJson($expected);
    }

    public function testUpdateUserInfoValidationFail()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class, 2)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst('id', 'asc');
        /**  @var User $updateUser */
        $updateUser = $this->userRepository->getFirst('id', 'desc');
        $userId = $updateUser->id;
        $path = action(sprintf('%s@updateUserInfo', $this->controllerName), $userId);
        $data = ['user' => $user];
        $response = $this->call('PATCH', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 422,
            'message' => [
                'errors' => [
                    'The full name field is required.',
                    'The role id field is required.',
                ]
            ]
        ];
        $response->assertStatus(422)
            ->assertJson($expected);
    }

    public function testUpdateUserInfoSuccessful()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::ADMIN_ROLE_NAME
        ]);
        /** @var Role $newRole */
        $newRole = factory(Role::class)->create([
            'name' => Role::NON_ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class, 2)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst('id', 'asc');
        /**  @var User $updateUser */
        $updateUser = $this->userRepository->getFirst('id', 'desc');
        $userId = $updateUser->id;
        $path = action(sprintf('%s@updateUserInfo', $this->controllerName), $userId);
        $data = [
            'user' => $user,
            'full_name' => 'updated name',
            'role_id' => $newRole->id
        ];
        $response = $this->call('PATCH', $path, $data);
        $response->assertSuccessful();
        $record = [
            'id' => $updateUser->id,
            'full_name' => 'updated name',
            'role_id' => $newRole->id
        ];
        $this->assertDatabaseHas('users', $record);
    }

    public function testDeleteUserNoAccess()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::NON_ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst();
        $userId = -1;
        $path = action(sprintf('%s@delete', $this->controllerName), $userId);
        $data = ['user' => $user];
        $response = $this->call('DELETE', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 403,
            'message' => [
                'errors' => [
                    'Your access to this user is forbidden.'
                ]
            ]
        ];
        $response->assertStatus(403)
            ->assertJson($expected);
    }

    public function testDeleteUserNotFound()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst();
        $userId = -1;
        $path = action(sprintf('%s@delete', $this->controllerName), $userId);
        $data = ['user' => $user];
        $response = $this->call('DELETE', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 422,
            'message' => [
                'errors' => [
                    'The specified user not found.'
                ]
            ]
        ];
        $response->assertStatus(422)
            ->assertJson($expected);
    }

    public function testDeleteUserMySelf()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class, 2)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst('id', 'asc');
        $userId = $user->id;
        $path = action(sprintf('%s@delete', $this->controllerName), $userId);
        $data = ['user' => $user];
        $response = $this->call('DELETE', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 422,
            'message' => [
                'errors' => [
                    'You cannot delete yourself.'
                ]
            ]
        ];
        $response->assertStatus(422)
            ->assertJson($expected);
    }

    public function testDeleteUserSuccessful()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create([
            'name' => Role::ADMIN_ROLE_NAME
        ]);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        factory(User::class, 2)->create([
            'role_id' => $role->id,
            'client_id' => $client->id,
        ]);
        /**  @var User $user */
        $user = $this->userRepository->getFirst('id', 'asc');
        /**  @var User $targetUser */
        $targetUser = $this->userRepository->getFirst('id', 'desc');
        $userId = $targetUser->id;
        $path = action(sprintf('%s@delete', $this->controllerName), $userId);
        $data = ['user' => $user];
        $response = $this->call('DELETE', $path, $data);
        $response->assertSuccessful();
        $this->assertDatabaseMissing('users', ['email' => $targetUser->email]);
        $this->assertDatabaseHas('users', ['email' => $targetUser->email . '_deleted']);
    }
}
