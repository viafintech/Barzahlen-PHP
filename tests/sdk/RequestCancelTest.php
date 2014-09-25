<?php
/**
 * Barzahlen Payment Module SDK
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     The MIT License (MIT) - http://opensource.org/licenses/MIT
 */

class RequestCancelTest extends PHPUnit_Framework_TestCase
{
    /**
     * Testing the construction of a cancel request array.
     */
    public function testBuildRequestArray()
    {
        $cancel = new Barzahlen_Request_Cancel('7691945');

        $requestArray = array('shop_id' => '10483',
            'transaction_id' => '7691945',
            'language' => 'de',
            'hash' => 'b344aebfb7b9c99c9894b096265f414cbd29223dd8314062fecdeedcd5e46b59f2906a7f5525b6564c85e42053063d49585ee1c108507304bc89b6e44623d44f');

        $this->assertEquals($requestArray, $cancel->buildRequestArray(SHOPID, PAYMENTKEY, 'de'));
    }

    /**
     * Testing XML parsing with a valid response.
     */
    public function testParseXmlWithValidResponse()
    {
        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7691945</transaction-id>
                      <result>0</result>
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
                    </response>';

        $cancel = new Barzahlen_Request_Cancel('7691945');
        $cancel->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertEquals('7691945', $cancel->getTransactionId());
        $this->assertTrue($cancel->isValid());
    }

    /**
     * Testing XML parsing with an error response.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testParseXmlWithErrorResponse()
    {
        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <result>6</result>
                      <error-message>transaction already paid</error-message>
                    </response>';

        $cancel = new Barzahlen_Request_Cancel('7691945');
        $cancel->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertFalse($cancel->isValid());
    }

    /**
     * Testing XML parsing with an empty response.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testParseXmlWithEmptyResponse()
    {
        $xmlResponse = '';

        $cancel = new Barzahlen_Request_Cancel('7691945');
        $cancel->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertFalse($cancel->isValid());
    }

    /**
     * Testing XML parsing with an incomplete response.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testParseXmlWithIncompleteResponse()
    {
        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7691945</transaction-id>
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
                    </response>';

        $cancel = new Barzahlen_Request_Cancel('7691945');
        $cancel->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertFalse($cancel->isValid());
    }

    /**
     * Testing XML parsing with an incorrect return value.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testParseXmlWithInvalidResponse()
    {
        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>1234567</transaction-id>
                      <result>0</result>
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
                    </response>';

        $cancel = new Barzahlen_Request_Cancel('7691945');
        $cancel->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertFalse($cancel->isValid());
    }

    /**
     * Testing XML parsing with an invalid xml response.
     *
     * @expectedException Barzahlen_Exception
     */
    public function testParseXmlWithInvalidXML()
    {
        $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7691945
                      <result>0
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8
                    </response>';

        $cancel = new Barzahlen_Request_Cancel('7691945');
        $cancel->parseXml($xmlResponse, PAYMENTKEY);

        $this->assertFalse($cancel->isValid());
    }

    /**
     * Tests that the right request type is returned.
     */
    public function testGetRequestType()
    {
        $cancel = new Barzahlen_Request_Cancel('7691945');
        $this->assertEquals('cancel', $cancel->getRequestType());
    }
}
