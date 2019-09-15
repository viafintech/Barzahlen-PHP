<?php

namespace Barzahlen\Tests\Exception;

use Barzahlen\Exception\ApiException;

class ApiExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testToString()
    {
        $apiException = new ApiException('An error occurred.', 'r3qu3s71d');
        $this->assertEquals('Barzahlen\Exception\ApiException: An error occurred. - RequestId: r3qu3s71d', $apiException->__toString());
    }
}
