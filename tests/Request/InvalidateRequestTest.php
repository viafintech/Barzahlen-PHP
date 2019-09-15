<?php

namespace Barzahlen\Tests\Request;

use Barzahlen\Request\InvalidateRequest;

class InvalidateRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var InvalidateRequest
     */
    private $request;


    public function setUp()
    {
        $this->request = new InvalidateRequest('slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd');
    }

    public function testIdempotenceRequired()
    {
        $this->assertFalse($this->request->getIdempotence());
    }

    public function testGetPath()
    {
        $this->assertEquals('/slips/slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd/invalidate', $this->request->getPath());
    }

    public function testGetMethod()
    {
        $this->assertEquals('POST', $this->request->getMethod());
    }

    public function testGetBody()
    {
        $this->assertNull($this->request->getBody());
    }
}
