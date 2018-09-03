<?php

declare (strict_types = 1);

namespace App\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}
