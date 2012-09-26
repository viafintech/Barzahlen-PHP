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
   * Set everything that is needed for the testing up.
   */
  public function setUp() {

    $this->api = $this->getMock('Barzahlen_Api', array('_sendRequest'), array(SHOPID, PAYMENTKEY));
    $this->resend = new Barzahlen_Request_Resend('7691945');
  }

  /**
   * Happy path test for a resend request.
   */
  public function testValidXmlResendResponse() {

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <response>
              <transaction-id>7691945</transaction-id>
              <result>0</result>
              <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
            </response>';

    $this->api->expects($this->once())
              ->method('_sendRequest')
              ->will($this->returnValue($xml));

    $this->api->handleRequest($this->resend);

    $this->assertEquals('7691945', $this->resend->getTransactionId());
    $this->assertTrue($this->resend->isValid());
  }

  /**
   * Receive empty xml response for a resend request.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testEmptyXmlResendResponse() {

    $xml = '';

    $this->api->expects($this->once())
              ->method('_sendRequest')
              ->will($this->returnValue($xml));

    $this->api->handleRequest($this->resend);
    $this->assertFalse($this->resend->isValid());
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {

    unset($this->api);
    unset($this->resend);
  }
}
?>