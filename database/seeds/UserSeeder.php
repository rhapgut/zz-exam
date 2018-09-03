<?php
/**
 * Created by PhpStorm.
 * User: yohei
 * Date: 20/4/18
 * Time: 2:24 PM
 */

use Illuminate\Database\Seeder;
use App\UseCase\UserUseCase;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Eloquent\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        /** @var UserUseCase $userUseCase */
        $userUseCase = App::make(UserUseCase::class);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = App::make(UserRepositoryInterface::class);

        /** @var RoleRepositoryInterface $roleRepository */
        $roleRepository = App::make(RoleRepositoryInterface::class);

        $adminRoleId = $roleRepository->getFirstByName(Role::ADMIN_ROLE_NAME)->id;
        $nonAdminRoleId = $roleRepository->getFirstByName(Role::NON_ADMIN_ROLE_NAME)->id;

        $userUseCase->createAndReturnClientUser([
            'email' => 'test@email.com',
            'password' => 'fdsafdsa',
            'full_name' => 'Test Name',
            'company_name' => 'Test Company',
            'is_authorized' => 1,
        ]);

        $userUseCase->createAndReturnUser([
            'email' => 'test2@email.com',
            'password' => 'fdsafdsa',
            'full_name' => 'Test 2',
            'is_authorized' => 1,
            'role_id' => 2,
            'client_id' => 1
        ]);

        $userUseCase->createAndReturnUser([
            'email' => 'test3@email.com',
            'password' => 'fdsafdsa',
            'full_name' => 'Test 3',
            'is_authorized' => 0,
            'role_id' => 2,
            'client_id' => 1
        ]);
    }
}