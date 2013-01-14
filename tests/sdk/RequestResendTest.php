<?php
/**
 * Barzahlen Payment Module SDK
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/
 *
 * @copyright   Copyright (c) 2012 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class RequestResendTest extends PHPUnit_Framework_TestCase {

  /**
   * Testing the construction of a resend request array.
   */
  public function testBuildRequestArray() {

    $resend = new Barzahlen_Request_Resend('7691945');

    $requestArray = array('shop_id' => '10483',
                          'transaction_id' => '7691945',
                          'language' => 'de',
                          'hash' => 'b344aebfb7b9c99c9894b096265f414cbd29223dd8314062fecdeedcd5e46b59f2906a7f5525b6564c85e42053063d49585ee1c108507304bc89b6e44623d44f');

    $this->assertEquals($requestArray, $resend->buildRequestArray(SHOPID, PAYMENTKEY, 'de'));
  }

  /**
   * Testing XML parsing with a valid response.
   */
  public function testParseXmlWithValidResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7691945</transaction-id>
                      <result>0</result>
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
                    </response>';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertEquals('7691945', $resend->getTransactionId());
    $this->assertTrue($resend->isValid());
  }

  /**
   * Testing XML parsing with an error response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithErrorResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <result>6</result>
                      <error-message>transaction already paid</error-message>
                    </response>';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($resend->isValid());
  }

  /**
   * Testing XML parsing with an empty response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithEmptyResponse() {

    $xmlResponse = '';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($resend->isValid());
  }

  /**
   * Testing XML parsing with an incomplete response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithIncompleteResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7691945</transaction-id>
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
                    </response>';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($resend->isValid());
  }

  /**
   * Testing XML parsing with an incorrect return value.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithInvalidResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>1234567</transaction-id>
                      <result>0</result>
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
                    </response>';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($resend->isValid());
  }

  /**
   * Testing XML parsing with an invalid xml response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithInvalidXML() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7691945
                      <result>0
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8
                    </response>';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($resend->isValid());
  }

  /**
   * Tests that the right request type is returned.
   */
  public function testGetRequestType() {

    $resend = new Barzahlen_Request_Resend('7691945');
    $this->assertEquals('resend_email', $resend->getRequestType());
  }
}
?>