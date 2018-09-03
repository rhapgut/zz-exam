<?php

declare(strict_types=1);

namespace Test\Feature\Eloquent\Repositories;

use App\Eloquent\Repositories\PhoneNumberRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneNumberRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @var PhoneNumberRepository */
    private $phoneNumberRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->phoneNumberRepository = $this->app->make(PhoneNumberRepository::class);
    }
}
