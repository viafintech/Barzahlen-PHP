<?php

namespace Barzahlen\Tests;

use Barzahlen\Webhook;

class WebhookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Webhook
     */
    private $webhook;

    /**
     * @var array
     */
    private $header;

    /**
     * @var string
     */
    private $body;


    public function setUp()
    {
        $this->webhook = new Webhook(PAYMENTKEY);

        $this->header = array(
            'HTTP_HOST' => 'callback.example.com',
            'SERVER_PORT' => 443,
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/barzahlen/callback',
            'QUERY_STRING' => '',
            'HTTP_DATE' => 'Fri, 01 Apr 2016 09:20:06 GMT'
        );

        $this->body = '{
    "event": "paid",
    "event_occurred_at": "2016-01-06T12:34:56Z",
    "affected_transaction_id": "4729294329",
    "slip": {
        "id": "slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd",
        "slip_type": "payment",
        "division_id": "1234",
        "reference_key": "O64737X",
        "expires_at": "2016-01-10T12:34:56Z",
        "customer": {
            "key": "LDFKHSLFDHFL",
            "cell_phone_last_4_digits": "6789",
            "email": "john@example.com",
            "language": "de-DE"
        },
        "metadata": {
          "order_id": 1234,
          "invoice_no": "A123"
        },
        "transactions": [
          {
            "id": "4729294329",
            "currency": "EUR",
            "amount": "123.34",
            "state": "paid"
          }
        ]
    }
}';
    }

    public function testValidSignature()
    {
        $this->header['HTTP_BZ_SIGNATURE'] = 'BZ1-HMAC-SHA256 8a82e3d2a4211a75a2c61c7b46911bffa9d219ab9b4e8a22eb8773ed27c7d230';

        $this->assertTrue($this->webhook->verify($this->header, $this->body));
    }

    public function testInvalidSignature()
    {
        $this->header['HTTP_BZ_SIGNATURE'] = 'BZ1-HMAC-SHA256 decc04eb22cda264a5cf5a138e8ac13f0aa8da2daf28c687d9db46872cf777f0';

        $this->assertFalse($this->webhook->verify($this->header, $this->body));
    }
}
