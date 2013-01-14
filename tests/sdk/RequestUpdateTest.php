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

class RequestUpdateTest extends PHPUnit_Framework_TestCase {

  /**
   * Testing the construction of an update request array.
   */
  public function testBuildRequestArray() {

    $update = new Barzahlen_Request_Update('7691945','42');

    $requestArray = array('shop_id' => '10483',
                          'transaction_id' => '7691945',
                          'order_id' => '42',
                          'hash' => '81b52a055e547294691bf9875e8f7cfcf4922c1c58ae8e34fdd059bc797eb23a82e9b96a045b253ad33cd4726b7e11cabbcf41a93c8667e662e7ce487e234d76');

    $this->assertEquals($requestArray, $update->buildRequestArray(SHOPID, PAYMENTKEY, 'de'));
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

    $update = new Barzahlen_Request_Update('7691945','42');
    $update->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertEquals('7691945', $update->getTransactionId());
    $this->assertTrue($update->isValid());
  }

  /**
   * Testing XML parsing with an error response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithErrorResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <result>8</result>
                      <error-message>order_id already set</error-message>
                    </response>';

    $update = new Barzahlen_Request_Update('7691945','42');
    $update->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($update->isValid());
  }

  /**
   * Testing XML parsing with an empty response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithEmptyResponse() {

    $xmlResponse = '';

    $update = new Barzahlen_Request_Update('7691945','42');
    $update->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($update->isValid());
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
                      <result>0</result>
                    </response>';

    $update = new Barzahlen_Request_Update('7691945','42');
    $update->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($update->isValid());
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

    $update = new Barzahlen_Request_Update('7691945','42');
    $update->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($update->isValid());
  }

  /**
   * Testing XML parsing with an invalid xml response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithInvalidXML() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7691945</transaction-id>
                      <result>0</result>
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>';

    $update = new Barzahlen_Request_Update('7691945','42');
    $update->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($update->isValid());
  }

  /**
   * Tests that the right request type is returned.
   */
  public function testGetRequestType() {

    $update = new Barzahlen_Request_Update('7691945','42');
    $this->assertEquals('update', $update->getRequestType());
  }
}
?>