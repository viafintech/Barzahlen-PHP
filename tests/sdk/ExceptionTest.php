<?php
/**
 * Barzahlen Payment Module SDK
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     The MIT License (MIT) - http://opensource.org/licenses/MIT
 */

class ExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Testing the correct output.
     */
    public function testToString()
    {
        $exception = new Barzahlen_Exception('An error occurred.', 42);
        $this->assertEquals("Barzahlen_Exception: [42] - An error occurred.\n", $exception->__toString());
    }
}
