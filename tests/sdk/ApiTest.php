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

class ApiTest extends PHPUnit_Framework_TestCase {

  /**
   * Set everything that is needed for the testing up.
   */
  public function setUp() {

    $this->api = $this->getMock('Barzahlen_Api', array('_sendRequest'), array(SHOPID, PAYMENTKEY));
    $this->api->setDebug(true, dirname(__FILE__) . '/../barzahlen.log');
  }

  /**
   * This tests the existence of default values.
   */
  public function testAttributeExistanceAndDefaultValues() {

    $this->assertAttributeEquals(SHOPID, '_shopId', $this->api);
    $this->assertAttributeEquals(PAYMENTKEY, '_paymentKey', $this->api);
    $this->assertAttributeEquals('de', '_language', $this->api);
    $this->assertAttributeEquals(false, '_sandbox', $this->api);
    $this->assertAttributeEquals(0, '_madeAttempts', $this->api);
  }

  /**
   * Tests language setter with and without value.
   */
  public function testSetLanguage() {

    $this->api->setLanguage('en');
    $this->assertAttributeEquals('en', '_language', $this->api);

    $this->api->setLanguage();
    $this->assertAttributeEquals('de', '_language', $this->api);

    $this->api->setLanguage('someRandomEntry');
    $this->assertAttributeEquals('de', '_language', $this->api);
  }

  /**
   * Testing request handling with a successful response on the first try.
   */
  public function testHandleRequest() {

    $requestArray = array('shop_id' => '10483',
                          'transaction_id' => '7691945',
                          'language' => 'de',
                          'hash' => 'b344aebfb7b9c99c9894b096265f414cbd29223dd8314062fecdeedcd5e46b59f2906a7f5525b6564c85e42053063d49585ee1c108507304bc89b6e44623d44f');

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <response>
              <transaction-id>7691945</transaction-id>
              <result>0</result>
              <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
            </response>';

    $request = $this->getMock('Barzahlen_Request_Resend', array('buildRequestArray', 'parseXml'), array('7691945'));

    $request->expects($this->once())
            ->method('buildRequestArray')
            ->will($this->returnValue($requestArray));

    $request->expects($this->once())
            ->method('parseXml')
            ->with($this->equalTo($xml), $this->equalTo(PAYMENTKEY));

    $this->api->expects($this->once())
              ->method('_sendRequest')
              ->will($this->returnValue($xml));

    $this->api->handleRequest($request);
  }

  /**
   * Testing request handling with a successful response on the second try.
   */
  public function testHandleRequestWithOneException() {

    $requestArray = array('shop_id' => '10483',
                          'transaction_id' => '7691945',
                          'language' => 'de',
                          'hash' => 'b344aebfb7b9c99c9894b096265f414cbd29223dd8314062fecdeedcd5e46b59f2906a7f5525b6564c85e42053063d49585ee1c108507304bc89b6e44623d44f');

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <response>
              <transaction-id>7691945</transaction-id>
              <result>0</result>
              <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
            </response>';

    $request = $this->getMock('Barzahlen_Request_Resend', array('buildRequestArray', 'parseXml'), array('7691945'));

    $request->expects($this->once())
            ->method('buildRequestArray')
            ->will($this->returnValue($requestArray));

    $request->expects($this->once())
            ->method('parseXml')
            ->with($this->equalTo($xml), $this->equalTo(PAYMENTKEY));

    $timeout = new Barzahlen_Exception('Error during cURL: connection timeout');

    $this->api->expects($this->exactly(2))
              ->method('_sendRequest')
              ->will($this->onConsecutiveCalls($this->throwException($timeout), $this->returnValue($xml)));

    $this->api->handleRequest($request);
  }

  /**
   * Testing request handling with none successful try but two timeout exceptions.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testHandleRequestWithTwoExceptions() {

    $requestArray = array('shop_id' => '10483',
                          'transaction_id' => '7691945',
                          'language' => 'de',
                          'hash' => 'b344aebfb7b9c99c9894b096265f414cbd29223dd8314062fecdeedcd5e46b59f2906a7f5525b6564c85e42053063d49585ee1c108507304bc89b6e44623d44f');

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <response>
              <transaction-id>7691945</transaction-id>
              <result>0</result>
              <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
            </response>';

    $request = $this->getMock('Barzahlen_Request_Resend', array('buildRequestArray', 'parseXml'), array('7691945'));

    $request->expects($this->once())
            ->method('buildRequestArray')
            ->will($this->returnValue($requestArray));

    $request->expects($this->never())
            ->method('parseXml');

    $timeout = new Barzahlen_Exception('Error during cURL: connection timeout');

    $this->api->expects($this->exactly(2))
              ->method('_sendRequest')
              ->will($this->throwException($timeout));

    $this->api->handleRequest($request);
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {

    unset($this->api);
    emptyLog();
  }
}
?>