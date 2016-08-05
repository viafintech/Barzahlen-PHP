<?php

namespace Barzahlen\Tests\Request;

use Barzahlen\Request\CreateRequest;

class CreateRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CreateRequest
     */
    private $request;


    public function setUp()
    {
        $this->request = new CreateRequest();
    }

    public function testIdempotenceRequired()
    {
        $this->assertTrue($this->request->getIdempotence());
    }

    public function testGetPath()
    {
        $this->assertEquals('/slips', $this->request->getPath());
    }

    public function testGetMethod()
    {
        $this->assertEquals('POST', $this->request->getMethod());
    }

    public function testSetMinimalJsonBody()
    {
        $body = '{
  "slip_type": "payment",
  "customer": {
    "key": "LDFKHSLFDHFL"
  },
  "transactions": [
    { "currency": "EUR", "amount": "123.34" }
  ]
}';

        $this->request->setBody($body);

        $this->assertEquals($body, $this->request->getBody());
    }

    public function testSetMinimalArrayBody()
    {
        $body = array(
            'slip_type' => 'payment',
            'customer' => array(
                'key' => 'LDFKHSLFDHFL'
            ),
            'transactions' => array(
                array(
                    'currency' => 'EUR',
                    'amount' => '123.34',
                )
            )
        );

        $this->request->setBody($body);

        $this->assertEquals(json_encode($body), $this->request->getBody());
    }

    public function testSetMinimalParametersBody()
    {
        $this->request->setSlipType('payment');
        $this->request->setCustomerKey('LDFKHSLFDHFL');
        $this->request->setAmount('123.34');
        $this->request->setCurrency('EUR');

        $expectedBody = '{"slip_type":"payment","transactions":[{"amount":"123.34","currency":"EUR"}],"customer":{"key":"LDFKHSLFDHFL"}}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }

    public function testSetCustomerAndMinimalParametersBody()
    {
        $this->request->setSlipType('payment');
        $this->request->setCustomer(array(
            'key' => 'LDFKHSLFDHFL',
            'cell_phone' => '012345678910'
        ));
        $this->request->setAmount('123.34');
        $this->request->setCurrency('EUR');

        $expectedBody = '{"slip_type":"payment","transactions":[{"amount":"123.34","currency":"EUR"}],"customer":{"key":"LDFKHSLFDHFL","cell_phone":"012345678910"}}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }

    public function testRefundBody()
    {
        $date = new \DateTime();
        $date->modify('+2 weeks');

        $this->request->setSlipType('refund');
        $this->request->setForSlipId('slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd');
        $this->request->setTransaction('-23.95', 'EUR');
        $this->request->setExpiresAt($date);

        $expectedBody = '{"slip_type":"refund","transactions":[{"amount":"-23.95","currency":"EUR"}],"refund":{"for_slip_id":"slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd"},"expires_at":"' . $date->format('c') . '"}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }

    public function testSetMaximalParametersBody()
    {
        $this->request->setSlipType('payment');
        $this->request->setReferenceKey('REFKEY123');
        $this->request->setHookUrl('https://www.example.tld/barzahlen/callback');
        $this->request->setExpiresAt('2016-04-01T12:34:56Z');
        $this->request->setCustomerKey('customer@provider.tld');
        $this->request->setCustomerCellPhone('01234567910');
        $this->request->setCustomerEmail('customer@provider.tld');
        $this->request->setCustomerLanguage('de_DE');
        $this->request->setAddress(array(
            'street_and_no' => 'Wallstr. 14a',
            'zipcode' => '10179',
            'city' => 'Berlin',
            'country' => 'DE'
        ));
        $this->request->setTransaction('123.45', 'EUR');
        $this->request->addMetadata('promo', 'summer2016');

        $expectedBody = '{"slip_type":"payment","transactions":[{"amount":"123.45","currency":"EUR"}],"customer":{"key":"customer@provider.tld","cell_phone":"01234567910","email":"customer@provider.tld","language":"de_DE"},"reference_key":"REFKEY123","hook_url":"https:\/\/www.example.tld\/barzahlen\/callback","expires_at":"2016-04-01T12:34:56Z","show_stores_near":{"address":{"street_and_no":"Wallstr. 14a","zipcode":"10179","city":"Berlin","country":"DE"}},"metadata":{"promo":"summer2016"}}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }
}
