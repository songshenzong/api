<?php

namespace Songshenzong\Api\Test;

use Songshenzong\Api\Api;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testError() : void
    {
        $this->assertInstanceOf(Api::class, new Api());
    }

}
