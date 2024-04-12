<?php

namespace Barzahlen\Tests;

use Barzahlen\Client;
use Barzahlen\Request\CreateRequest;
use Barzahlen\Request\InvalidateRequest;

use ReflectionClass;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $userAgent = 'PHP SDK v2.2.0';

    public function setUp(): void
    {
        $this->client = new Client(DIVISIONID, PAYMENTKEY);
    }

    public function testDefaultUserAgent()
    {
        $reflection = new ReflectionClass($this->client);
        $property = $reflection->getProperty('userAgent');
        $property->setAccessible(true);
        $actualUserAgent = $property->getValue($this->client);

        $this->assertEquals($this->userAgent, $actualUserAgent);
    }

    public function testSetUserAgent()
    {
        $this->client->setUserAgent('Shopsystem v2.2.0');

        $reflection = new ReflectionClass($this->client);
        $property = $reflection->getProperty('userAgent');
        $property->setAccessible(true);
        $actualUserAgent = $property->getValue($this->client);

        $this->assertEquals('Shopsystem v2.2.0', $actualUserAgent);
    }

    public function testBuildHeaderWithIdempotency()
    {
        $request = new CreateRequest();
        $request->setSlipType('payment');
        $request->setCustomerKey('UNIQUEKEY');
        $request->setTransaction('24.95', 'EUR');

        $header = $this->client->buildHeader($request);
        $this->assertEquals('Host: api.viafintech.com', $header[0]);
        $this->assertStringContainsString('Date: ', $header[1]);
        $this->assertEquals('User-Agent: '.$this->userAgent, $header[2]);
        $this->assertMatchesRegularExpression('/^Authorization: BZ1-HMAC-SHA256 DivisionId=12345, Signature=[a-f0-9]{64}$/', $header[3]);
        $this->assertMatchesRegularExpression('/^Idempotency-Key: [a-f0-9]{32}$/', $header[4]);
    }

    public function testBuildHeaderWithoutIdempotencyForSandbox()
    {
        $request = new InvalidateRequest('slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd');
        $client = new Client(DIVISIONID, PAYMENTKEY, true);

        $header = $client->buildHeader($request);
        $this->assertEquals('Host: api-sandbox.viafintech.com', $header[0]);
        $this->assertStringContainsString('Date: ', $header[1]);
        $this->assertEquals('User-Agent: '.$this->userAgent, $header[2]);
        $this->assertMatchesRegularExpression('/^Authorization: BZ1-HMAC-SHA256 DivisionId=12345, Signature=[a-f0-9]{64}$/', $header[3]);
        $this->assertArrayNotHasKey(4, $header);
    }

    public function testNoneError()
    {
        $response = '{}';
        $this->assertNull($this->client->checkResponse($response, 'application/json;charset=utf-8'));
    }

    public function testAuthError()
    {
        $this->expectException(\Barzahlen\Exception\AuthException::class);
        $this->expectExceptionMessage("error message");

        $response = '{"error_class":"auth","error_code":"invalid_signature","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testIdempotencyError()
    {
        $this->expectException(\Barzahlen\Exception\IdempotencyException::class);
        $this->expectExceptionMessage("error message");

        $response = '{"error_class":"idempotency","error_code":"use_idempotency_key_twice","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testInvalidFormatError()
    {
        $this->expectException(\Barzahlen\Exception\InvalidFormatException::class);
        $this->expectExceptionMessage("error message");

        $response = '{"error_class":"invalid_format","error_code":"bad_json_format","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testInvalidParameterError()
    {
        $this->expectException(\Barzahlen\Exception\InvalidParameterException::class);
        $this->expectExceptionMessage("error message");

        $response = '{"error_class":"invalid_parameter","error_code":"invalid_slip_type","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testInvalidStateError()
    {
        $this->expectException(\Barzahlen\Exception\InvalidStateException::class);
        $this->expectExceptionMessage("error message");

        $response = '{"error_class":"invalid_state","error_code":"invalid_slip_state","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testNotAllowedError()
    {
        $this->expectException(\Barzahlen\Exception\NotAllowedException::class);
        $this->expectExceptionMessage("error message");

        $response = '{"error_class":"not_allowed","error_code":"method_not_allowed","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testRateLimitError()
    {
        $this->expectException(\Barzahlen\Exception\RateLimitException::class);
        $this->expectExceptionMessage("error message");

        $response = '{"error_class":"rate_limit","error_code":"rate_limit_exceeded","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testServerError()
    {
        $this->expectException(\Barzahlen\Exception\ServerException::class);
        $this->expectExceptionMessage("error message");

        $response = '{"error_class":"server_error","error_code":"internal_server_error","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testTransportError()
    {
        $this->expectException(\Barzahlen\Exception\TransportException::class);
        $this->expectExceptionMessage("error message");

        $response = '{"error_class":"transport","error_code":"invalid_host_header","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testUnknownError()
    {
        $this->expectException(\Barzahlen\Exception\ApiException::class);
        $this->expectExceptionMessage("error message");

        $response = '{"error_class":"unknown","error_code":"unknown_error","message":"error message","request_id":"r3qu3s71d"}';
        $this->client->checkResponse($response, 'application/json;charset=utf-8');
    }

    public function testMediaResponse()
    {
        $response = ''; // some pdf data - non in this test case
        $this->assertNull($this->client->checkResponse($response, 'application/pdf'));
    }
}
