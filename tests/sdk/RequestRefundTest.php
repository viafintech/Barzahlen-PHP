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
 * @author      Alexander Diebler (alexander.diebler@barzahlen.de)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class RequestRefundTest extends PHPUnit_Framework_TestCase {

  /**
   * Set everything that is needed for the testing up.
   */
  public function setUp() {

    $this->api = $this->getMock('Barzahlen_Api', array('_sendRequest'), array(SHOPID, PAYMENTKEY));
    $this->refund = new Barzahlen_Request_Refund('7690927', '24.95');
  }

  /**
   * Happy path test for a refund request.
   */
  public function testValidXmlRefundResponse() {

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <response>
              <origin-transaction-id>7690927</origin-transaction-id>
              <refund-transaction-id>7691945</refund-transaction-id>
              <result>0</result>
              <hash>f53bff1be34d4d98fef8660d6bdf6988b55d14e81163b4c9e983abee09d24304a46edc79d1e19f3c45bc5c2265ac740d092210c1d278999808c470b59e61ef79</hash>
            </response>';

    $this->api->expects($this->once())
              ->method('_sendRequest')
              ->will($this->returnValue($xml));

    $this->api->handleRequest($this->refund);

    $this->assertEquals('7690927', $this->refund->getOriginTransactionId());
    $this->assertEquals('7691945', $this->refund->getRefundTransactionId());
    $this->assertEquals(array('origin-transaction-id' => '7690927', 'refund-transaction-id' => '7691945'),
                        $this->refund->getXmlArray());
    $this->assertTrue($this->refund->isValid());
  }

  /**
   * Receive error xml response for a refund request.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testErrorXmlRefundResponse() {

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <response>
              <result>22</result>
              <error-message>amount not valid</error-message>
            </response>';

    $this->api->expects($this->once())
              ->method('_sendRequest')
              ->will($this->returnValue($xml));

    $this->api->handleRequest($this->refund);
    $this->assertFalse($this->refund->isValid());
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {

    unset($this->api);
    unset($this->refund);
  }
}
?>