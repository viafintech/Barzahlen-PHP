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

class RequestRefundTest extends PHPUnit_Framework_TestCase {

  /**
   * Set everything that is needed for the testing up.
   */
  public function setUp() {
    
    $this->object = $this->getMock('BZ_SDK_Barzahlen', array('_sendRequest'), array(ShopId, PaymentKey, NotificationKey));
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
    
    $this->object->expects($this->once())
                 ->method('_sendRequest')
                 ->will($this->returnValue($xml));
                
    $this->object->refund('7690927', '24.95');
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {

    unset($this->object);
  }
}
?>
  