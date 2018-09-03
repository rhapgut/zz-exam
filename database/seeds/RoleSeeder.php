<?php
/**
 * Created by PhpStorm.
 * User: yohei
 * Date: 20/4/18
 * Time: 2:00 PM
 */

use Illuminate\Database\Seeder;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Eloquent\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /** @var RoleRepositoryInterface $roleRepository */
        $roleRepository = App::make(RoleRepositoryInterface::class);

        $roleRepository->create([
            'name' => Role::ADMIN_ROLE_NAME
        ]);

        $roleRepository->create([
            'name' => Role::NON_ADMIN_ROLE_NAME
        ]);
    }
}