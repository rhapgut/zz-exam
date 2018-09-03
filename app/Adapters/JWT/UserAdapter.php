<?php

namespace App\Adapters\JWT;

use App\Eloquent\Models\User;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Providers\User\UserInterface;

/**
 * Created by PhpStorm.
 * User: yohei
 * Date: 28/8/18
 * Time: 1:35 PM
 */

class UserAdapter implements UserInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $user;

    /**
     * Create a new User instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $user
     */
    public function __construct(Model $user)
    {
        $this->user = $user;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return User
     */
    public function getBy($key, $value)
    {
        return $this->user->where($key, $value)->with(['phoneNumbers'])->first();
    }
}
