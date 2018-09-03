<?php

declare (strict_types = 1);

namespace App\Eloquent\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'role_id',
        'email',
        'full_name',
        'password',
        'is_authorized',
        'client_id',
        'is_deleted'
    ];

    protected $hidden = [
        'id',
        'role_id',
        'client_id',
        'password',
        'full_name'
    ];

    /**
     * @return HasOne
     */
    public function role(): HasOne
    {
        return $this->hasOne('App\Eloquent\Models\Role', 'id', 'role_id');
    }

    /**
     * @return HasOne
     */
    public function client(): HasOne
    {
        return $this->hasOne('App\Eloquent\Models\Client', 'id', 'client_id');
    }

    /**
     * @return HasMany
     */
    public function phoneNumbers(): HasMany
    {
        return $this->hasMany('App\Eloquent\Models\UserPhoneNumber', 'user_id', 'id');
    }
}
