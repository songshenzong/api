<?php

namespace Songshenzong\Api\Test;

use PHPUnit\Framework\TestCase;
use Songshenzong\Api\Api;

class ApiTest extends TestCase
{

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testError(): void
    {
        $this->assertInstanceOf(Api::class, new Api());
    }

}
