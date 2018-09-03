<?php

namespace App\UseCase;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\UserPhoneNumberRepositoryInterface;
use App\Contracts\Repositories\ClientRepositoryInterface;
use App\Eloquent\Models\Role;
use App\Eloquent\Models\User;

class UserUseCase
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var UserPhoneNumberRepositoryInterface
     */
    private $userPhoneNumberRepository;

    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var ClientRepositoryInterface
     */
    private $clientRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserPhoneNumberRepositoryInterface $userStatusRepository,
        RoleRepositoryInterface $roleRepository,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userPhoneNumberRepository = $userStatusRepository;
        $this->roleRepository = $roleRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @param array $data
     * @return User
     */
    public function createAndReturnClientUser(array $data): User
    {
        $client = $this->clientRepository->createAndReturn([
            'name' => $data['company_name'],
            'address' => $data['company_address'] ?? ''
        ]);
        $data['role_id'] = $this->roleRepository->getFirstByName(Role::ADMIN_ROLE_NAME)->id;
        $data['client_id'] = $client->id;
        $user = $this->userRepository->createAndReturnUser($data);
        return $user;
    }

    /**
     * @param array $data
     * @return User
     */
    public function createAndReturnUser(array $data): User
    {
        $user = $this->userRepository->createAndReturnUser($data);
        return $user;
    }

    public function deleteUser(User $user)
    {
       
    }
}
