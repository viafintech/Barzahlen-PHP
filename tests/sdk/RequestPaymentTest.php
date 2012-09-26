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

class RequestPaymentTest extends PHPUnit_Framework_TestCase {

  /**
   * Set everything that is needed for the testing up.
   */
  public function setUp() {

    $this->api = $this->getMock('Barzahlen_Api', array('_sendRequest'), array(SHOPID, PAYMENTKEY));
    $this->payment = new Barzahlen_Request_Payment('foo@bar.com', '1', '24.95');
  }

  /**
   * Happy path test for a payment request.
   */
  public function testValidXmlPaymentResponse() {

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <response>
              <transaction-id>7690927</transaction-id>
              <payment-slip-link>https://api-online-sandbox.barzahlen.de:904/download/2001048300000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
              <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
              <infotext-1><![CDATA[Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß]]></infotext-1>
              <infotext-2><![CDATA[Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter]]></infotext-2>
              <result>0</result>
              <hash>5a175d4002e91f4b16758ff4b8b41ff973ad355e48e73d386195cb8605600d18e443819c4e7044ebb5853a45ff9ffe75b6868e33cc98459494b656301991c18e</hash>
            </response>';

    $this->api->expects($this->once())
              ->method('_sendRequest')
              ->will($this->returnValue($xml));

    $this->api->setDebug(true, __DIR__ . "/../barzahlen.log");

    $this->api->handleRequest($this->payment);

    $this->assertEquals('7690927', $this->payment->getTransactionId());
    $this->assertEquals('https://api-online-sandbox.barzahlen.de:904/download/2001048300000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf', $this->payment->getPaymentSlipLink());
    $this->assertEquals('Der Zahlschein ist 14 Tage gültig.', $this->payment->getExpirationNotice());
    $this->assertEquals('Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß', $this->payment->getInfoText1());
    $this->assertEquals('Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter', $this->payment->getInfoText2());
    $this->assertTrue($this->payment->isValid());
  }

  /**
   * Invalid hash test for a payment request.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testInvalidHashXmlPaymentResponse() {

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <response>
              <transaction-id>7690927</transaction-id>
              <payment-slip-link>https://api-online-sandbox.barzahlen.de:904/download/2001048300000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
              <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
              <infotext-1><![CDATA[Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß]]></infotext-1>
              <infotext-2><![CDATA[Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter]]></infotext-2>
              <result>0</result>
              <hash>5a175d4002e91f4b16758ff4b8b41ff973ad355e48e73d386195cb8605600d18e443819c4e7044ebb5853a45ff9ffe75b6868e33cc98459494b656301991c18f</hash>
            </response>';

    $this->api->expects($this->once())
              ->method('_sendRequest')
              ->will($this->returnValue($xml));

    $this->api->handleRequest($this->payment);
    $this->assertFalse($this->payment->isValid());
  }

  /**
   * Error test for a payment request.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testErrorXmlPaymentResponse() {

    $this->api->expects($this->any())
              ->method('_sendRequest')
              ->will($this->throwException(new Exception("Timeout")));

    $this->api->handleRequest($this->payment);
    $this->assertFalse($this->payment->isValid());
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {

    unset($this->api);
    unset($this->payment);
    emptyLog();
  }
}
?>