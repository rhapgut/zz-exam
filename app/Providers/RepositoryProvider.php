<?php
declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\ClientRepositoryInterface;
use App\Eloquent\Repositories\ClientRepository;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\UserPhoneNumberRepositoryInterface;
use App\Eloquent\Repositories\UserRepository;
use App\Eloquent\Repositories\UserPhoneNumberRepository;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Eloquent\Repositories\RoleRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(UserRepositoryInterface::class, function () {
            return new UserRepository();
        });
        $this->app->singleton(RoleRepositoryInterface::class, function () {
            return new RoleRepository();
        });
        $this->app->singleton(UserPhoneNumberRepositoryInterface::class, function () {
            return new UserPhoneNumberRepository();
        });
        $this->app->singleton(ClientRepositoryInterface::class, function () {
            return new ClientRepository();
        });
    }
}