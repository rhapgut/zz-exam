<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Eloquent\Models\Client;
use App\Eloquent\Models\Role;
use App\Eloquent\Models\User;
use App\Eloquent\Repositories\UserRepository;
use App\Eloquent\Repositories\UserPhoneNumberRepository;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserPhoneNumberControllerTest extends TestCase
{
    use WithoutMiddleware;

    /** @var string */
    private $controllerName;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserPhoneNumberRepository */
    private $phoneNumberRepository;

    public function setUp()
    {
        parent::setUp();
        $this->controllerName = 'Api\UserPhoneNumberController';
        $this->userRepository = $this->app->make(UserRepository::class);
        $this->phoneNumberRepository = $this->app->make(UserPhoneNumberRepository::class);
    }

    public function testCreatePhoneNumberValidationFail()
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
            'code' => 422,
            'message' => [
                'errors' => [
                    'The number field is required.',
                ]
            ]
        ];
        $response->assertStatus(422)
            ->assertJson($expected);
    }

    public function testCreatePhoneNumberInvalidFormat()
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
        $data = [
            'user' => $user,
            'number' => '123456789'
        ];
        $response = $this->call('POST', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 422,
            'message' => [
                'errors' => [
                    'The number format is invalid.',
                ]
            ]
        ];
        $response->assertStatus(422)
            ->assertJson($expected);
    }

    public function testCreatePhoneNumberAlreadyExist()
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
        // add phone number
        $phoneNumber = '09123456789';
        $this->phoneNumberRepository->create([
            'user_id' => $user->id,
            'number' => $phoneNumber
        ]);
        $path = action(sprintf('%s@store', $this->controllerName));
        $data = [
            'user' => $user,
            'number' => $phoneNumber
        ];
        $response = $this->call('POST', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 422,
            'message' => [
                'errors' => [
                    'The phone number is already exist.',
                ]
            ]
        ];
        $response->assertStatus(422)
            ->assertJson($expected);
    }

    public function testCreatePhoneNumberSuccessful()
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
        $phoneNumber = '09123456789';
        $data = [
            'user' => $user,
            'number' => $phoneNumber
        ];
        $response = $this->call('POST', $path, $data);
        $response->assertSuccessful();
        $record = [
            'user_id' => $user->id,
            'number' => $phoneNumber
        ];
        $this->assertDatabaseHas('user_phone_numbers', $record);
    }

    public function testDeletePhoneNumberNotFound()
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
        $userPhoneNumberId = -1;
        $path = action(sprintf('%s@delete', $this->controllerName), $userPhoneNumberId);
        $data = ['user' => $user];
        $response = $this->call('DELETE', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 422,
            'message' => [
                'errors' => [
                    'The specified phone number is not found.',
                ]
            ]
        ];
        $response->assertStatus(422)
            ->assertJson($expected);
    }

    public function testDeletePhoneNumberNoAccess()
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
        /**  @var User $user */
        $user = $this->userRepository->getFirst('id', 'asc');
        /**  @var User $userTwo */
        $userTwo = $this->userRepository->getFirst('id', 'desc');
        $number = '09123456789';
        $phoneNumber = $this->phoneNumberRepository->createAndReturn([
            'user_id' => $userTwo->id,
            'number' => $number
        ]);
        $userPhoneNumberId = $phoneNumber->id;
        $path = action(sprintf('%s@delete', $this->controllerName), $userPhoneNumberId);
        $data = ['user' => $user];
        $response = $this->call('DELETE', $path, $data);
        $expected = [
            'status' => 'fail',
            'code' => 403,
            'message' => [
                'errors' => [
                    'Your access to this phone number is forbidden.',
                ]
            ]
        ];
        $response->assertStatus(403)
            ->assertJson($expected);
    }

    public function testDeletePhoneNumberSuccessful()
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
        $number = '09123456789';
        $phoneNumber = $this->phoneNumberRepository->createAndReturn([
            'user_id' => $user->id,
            'number' => $number
        ]);
        $userPhoneNumberId = $phoneNumber->id;
        $path = action(sprintf('%s@delete', $this->controllerName), $userPhoneNumberId);
        $data = ['user' => $user];
        $response = $this->call('DELETE', $path, $data);
        $response->assertSuccessful();
        $record = [
            'user_id' => $user->id,
            'number' => $number
        ];
        $this->assertDatabaseMissing('user_phone_numbers', $record);
    }
}
