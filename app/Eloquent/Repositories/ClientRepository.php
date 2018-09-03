<?php

declare (strict_types = 1);

namespace App\Eloquent\Repositories;

use App\Contracts\Repositories\ClientRepositoryInterface;
use App\Eloquent\Models\Client;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    public function __construct()
    {
        $this->setModel(new Client());
    }
}
