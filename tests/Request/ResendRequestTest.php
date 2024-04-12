<?php

namespace Barzahlen\Tests\Request;

use Barzahlen\Request\ResendRequest;

class ResendRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResendRequest
     */
    private $requestEmail;

    /**
     * @var ResendRequest
     */
    private $requestTextMessage;


    public function setUp(): void
    {
        $this->requestEmail = new ResendRequest('slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd', 'email');
        $this->requestTextMessage = new ResendRequest('slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd', 'text_message');
    }

    public function testIdempotenceRequired()
    {
        $this->assertFalse($this->requestEmail->getIdempotence());
        $this->assertFalse($this->requestTextMessage->getIdempotence());
    }

    public function testGetPath()
    {
        $this->assertEquals('/slips/slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd/resend/email', $this->requestEmail->getPath());
        $this->assertEquals('/slips/slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd/resend/text_message', $this->requestTextMessage->getPath());
    }

    public function testGetMethod()
    {
        $this->assertEquals('POST', $this->requestEmail->getMethod());
        $this->assertEquals('POST', $this->requestTextMessage->getMethod());
    }

    public function testGetBody()
    {
        $this->assertNull($this->requestEmail->getBody());
        $this->assertNull($this->requestTextMessage->getBody());
    }
}
