<?php

namespace Songshenzong\Api\Test;

use Songshenzong\Api\Api;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

class ApiTest extends TestCase
{

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testError() : void
    {
        $this->assertInstanceOf(Api::class, new Api());
    }

}
