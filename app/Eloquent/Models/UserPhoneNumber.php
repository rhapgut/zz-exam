<?php

declare (strict_types = 1);

namespace App\Eloquent\Models;

class UserPhoneNumber extends Base
{
    protected $table = 'user_phone_numbers';

    protected $fillable = [
        'user_id',
        'number',
    ];
}
