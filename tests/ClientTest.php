<?php

namespace Barzahlen\Tests;

use Barzahlen\Client;
use Barzahlen\Request\CreateRequest;
use Barzahlen\Request\InvalidateRequest;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $userAgent = 'PHP SDK v2.1.2';

    public function setUp()
    {
        $this->client = new Client(DIVISIONID, PAYMENTKEY);
    }

    public function testDefaultUserAgent()
    {
        $this->assertAttributeEquals($this->userAgent, 'userAgent', $this->client);
    }

    public function testSetUserAgent()
    {
        $this->client->setUserAgent('Shopsystem v2.1.2');

        $this->assertAttributeEquals('Shopsystem v2.1.2', 'userAgent', $this->client);
    }

    public function testBuildHeaderWithIdempotency()
    {
        $request = new CreateRequest();
        $request->setSlipType('payment');
        $request->setCustomerKey('UNIQUEKEY');
        $request->setTransaction('24.95', 'EUR');

        $header = $this->client->buildHeader($request);
        $this->assertEquals('Host: api.barzahlen.de', $header[0]);
        $this->assertContains('Date: ', $header[1]);
        $this->assertEquals('User-Agent: '.$this->userAgent, $header[2]);
        $this->assertRegExp('/^Authorization: BZ1-HMAC-SHA256 DivisionId=12345, Signature=[a-f0-9]{64}$/', $header[3]);
        $this->assertRegExp('/^Idempotency-Key: [a-f0-9]{32}$/', $header[4]);
    }

    public function testBuildHeaderWithoutIdempotencyForSandbox()
    {
        $request = new InvalidateRequest('slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd');
        $client = new Client(DIVISIONID, PAYMENTKEY, true);

        $header = $client->buildHeader($request);
        $this->assertEquals('Host: api-sandbox.barzahlen.de', $header[0]);
        $this->assertContains('Date: ', $header[1]);
        $this->assertEquals('User-Agent: '.$this->userAgent, $header[2]);
        $this->assertRegExp('/^Authorization: BZ1-HMAC-SHA256 DivisionId=12345, Signature=[a-f0-9]{64}$/', $header[3]);
        $this->assertArrayNotHasKey(4, $header);
    }

    public function testNoneError()
    {
        $response = '{}';
        $this->assertNull($this->client->checkResponse($response, 'application/json;charset=utf-8'));
    }

    /**
     * @expectedException \Barzahlen\Exception\AuthException
     */
    public function testAuthError()
    {
        $response = '{"error_class":"auth","error_code":"invalid_signature","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    /**
     * @expectedException \Barzahlen\Exception\IdempotencyException
     */
    public function testIdempotencyError()
    {
        $response = '{"error_class":"idempotency","error_code":"use_idempotency_key_twice","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    /**
     * @expectedException \Barzahlen\Exception\InvalidFormatException
     */
    public function testInvalidFormatError()
    {
        $response = '{"error_class":"invalid_format","error_code":"bad_json_format","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    /**
     * @expectedException \Barzahlen\Exception\InvalidParameterException
     */
    public function testInvalidParameterError()
    {
        $response = '{"error_class":"invalid_parameter","error_code":"invalid_slip_type","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    /**
     * @expectedException \Barzahlen\Exception\InvalidStateException
     */
    public function testInvalidStateError()
    {
        $response = '{"error_class":"invalid_state","error_code":"invalid_slip_state","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    /**
     * @expectedException \Barzahlen\Exception\NotAllowedException
     */
    public function testNotAllowedError()
    {
        $response = '{"error_class":"not_allowed","error_code":"method_not_allowed","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    /**
     * @expectedException \Barzahlen\Exception\RateLimitException
     */
    public function testRateLimitError()
    {
        $response = '{"error_class":"rate_limit","error_code":"rate_limit_exceeded","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    /**
     * @expectedException \Barzahlen\Exception\ServerException
     */
    public function testServerError()
    {
        $response = '{"error_class":"server_error","error_code":"internal_server_error","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    /**
     * @expectedException \Barzahlen\Exception\TransportException
     */
    public function testTransportError()
    {
        $response = '{"error_class":"transport","error_code":"invalid_host_header","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    /**
     * @expectedException \Barzahlen\Exception\ApiException
     */
    public function testUnknownError()
    {
        $response = '{"error_class":"unknown","error_code":"unknown_error","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testMediaResponse()
    {
        $response = ''; // some pdf data - non in this test case
        $this->assertNull($this->client->checkResponse($response, 'application/pdf'));
    }
}
