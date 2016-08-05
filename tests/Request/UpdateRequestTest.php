<?php

namespace Barzahlen\Tests\Request;

use Barzahlen\Request\UpdateRequest;

class UpdateRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateRequest
     */
    private $request;


    public function setUp()
    {
        $this->request = new UpdateRequest('slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd');
    }

    public function testIdempotenceRequired()
    {
        $this->assertFalse($this->request->getIdempotence());
    }

    public function testGetPath()
    {
        $this->assertEquals('/slips/slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd', $this->request->getPath());
    }

    public function testGetMethod()
    {
        $this->assertEquals('PATCH', $this->request->getMethod());
    }

    public function testGetBody()
    {
        $this->assertEquals('[]', $this->request->getBody());
    }

    public function testSetBodyJson()
    {
        $json = '{"reference_key":"NEWKEY"}';
        $this->request->setBody($json);

        $this->assertEquals($json, $this->request->getBody());
    }

    public function testSetBodyArray()
    {
        $array = array(
            'reference_key' => 'NEWKEY'
        );
        $this->request->setBody($array);

        $this->assertEquals('{"reference_key":"NEWKEY"}', $this->request->getBody());
    }

    public function testSetCustomer()
    {
        $this->request->setCustomer(array(
            'email' => 'customer@provider.tld',
            'cell_phone' => '012345678910'
        ));

        $expectedBody = '{"customer":{"email":"customer@provider.tld","cell_phone":"012345678910"}}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }

    public function testSetCustomerCellPhone()
    {
        $this->request->setCustomerCellPhone('012345678910');

        $expectedBody = '{"customer":{"cell_phone":"012345678910"}}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }

    public function testSetCustomerEmail()
    {
        $this->request->setCustomerEmail('customer@provider.tld');

        $expectedBody = '{"customer":{"email":"customer@provider.tld"}}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }

    public function testSetExpiresAt()
    {
        $date = new \DateTime();
        $date->modify('+1 week');

        $this->request->setExpiresAt($date);

        $expectedBody = '{"expires_at":"' . $date->format('c') . '"}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }

    public function testSetReferenceKey()
    {
        $this->request->setReferenceKey('NEWKEY');

        $expectedBody = '{"reference_key":"NEWKEY"}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }

    public function testSetTransaction()
    {
        $this->request->setTransactionId('4729294329');
        $this->request->setAmount('150.00');

        $expectedBody = '{"transactions":[{"id":"4729294329","amount":"150.00"}]}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }

    public function testSetTransactionCombined()
    {
        $this->request->setTransaction('4729294329', '150.00');

        $expectedBody = '{"transactions":[{"id":"4729294329","amount":"150.00"}]}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }

    public function testSetMaximalParametersBody()
    {
        $this->request->setCustomer(array(
            'email' => 'customer@provider.tld',
            'cell_phone' => '012345678910'
        ));
        $this->request->setExpiresAt('2016-01-10T12:34:56Z');
        $this->request->setTransaction('4729294329', '150.00');
        $this->request->setReferenceKey('NEWKEY');

        $expectedBody = '{"customer":{"email":"customer@provider.tld","cell_phone":"012345678910"},"expires_at":"2016-01-10T12:34:56Z","reference_key":"NEWKEY","transactions":[{"id":"4729294329","amount":"150.00"}]}';

        $this->assertEquals($expectedBody, $this->request->getBody());
    }
}
