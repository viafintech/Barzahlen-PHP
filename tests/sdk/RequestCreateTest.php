<?php
/**
 * Bar zahlen Payment Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@barzahlen.de so we can send you a copy immediately.
 *
 * @category    ZerebroInternet
 * @package     ZerebroInternet_Barzahlen
 * @copyright   Copyright (c) 2012 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Alexander Diebler (alexander.diebler@barzahlen.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL-3.0)
 */

class RequestCreateTest extends PHPUnit_Framework_TestCase {

  /**
   * Set everything that is needed for the testing up.
   */
  public function setUp() {
    
    $this->object = $this->getMock('BZ_SDK_Barzahlen', array('_sendRequest'), array(ShopId, PaymentKey, NotificationKey));
  }

  /**
   * Happy path test for a create request.
   */
  public function testValidXmlCreateResponse() {

    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <response>
              <transaction-id>7690927</transaction-id>
              <payment-slip-link>https://api-online-sandbox.barzahlen.de:904/download/2001048300000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
              <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
              <infotext-1>Hallo &lt;b&gt;Welt&lt;/b&gt;! &lt;a href="http://www.barzahlen.de"&gt;Bar zahlen&lt;/a&gt; Infütöxt Äinß</infotext-1>
              <infotext-2>Hallo &lt;i&gt;Welt&lt;/i&gt;! &lt;a href="http://www.barzahlen.de?a=b&amp;c=d"&gt;Bar zahlen&lt;/a&gt; Infütöxt 2% &amp; so weiter</infotext-2>
              <result>0</result>
              <hash>5a175d4002e91f4b16758ff4b8b41ff973ad355e48e73d386195cb8605600d18e443819c4e7044ebb5853a45ff9ffe75b6868e33cc98459494b656301991c18e</hash>
            </response>';
    
    $this->object->expects($this->once())
                 ->method('_sendRequest')
                 ->will($this->returnValue($xml));
                
    $this->object->create('foo@bar.com', '1', '24.95');
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {

    unset($this->object);
  }
}
?>
  