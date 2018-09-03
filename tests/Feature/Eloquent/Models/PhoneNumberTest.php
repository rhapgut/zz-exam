<?php

declare(strict_types=1);

namespace Test\Feature\Eloquent\Models;

use App\Eloquent\Models\PhoneNumber;
use App\Eloquent\Repositories\PhoneNumberRepository;
use Tests\TestCase;

class PhoneNumberTest extends TestCase
{
    /** @var PhoneNumberRepository */
    private $phoneNumberRepository;

    public function setUp()
    {
        parent::setUp();
        $this->phoneNumberRepository = $this->app->make(PhoneNumberRepository::class);
    }
}
