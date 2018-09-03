<?php

declare (strict_types = 1);

namespace App\Eloquent\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Base
{
    protected $table = 'clients';

    protected $fillable = [
        'name',
        'address',
    ];

    protected $hidden = [
        'id',
        'name',
        'address',
    ];

    /**
     * @return HasMany
     */
    public function users(array $with = []): HasMany
    {
        return $this->hasMany('App\Eloquent\Models\User', 'client_id', 'id')->with($with);
    }
}
