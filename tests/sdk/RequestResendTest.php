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

class RequestResendTest extends PHPUnit_Framework_TestCase {

  /**
   * Set everything that is needed for the testing up.
   */
  public function setUp() {
    
    $this->object = $this->getMock('BZ_SDK_Barzahlen', array('_sendRequest'), array(ShopId, PaymentKey, NotificationKey));
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
    
    $this->object->expects($this->once())
                 ->method('_sendRequest')
                 ->will($this->returnValue($xml));
                
    $this->object->resend('7691945');
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {

    unset($this->object);
  }
}
?>
  