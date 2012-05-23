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

class NotificationTest extends PHPUnit_Framework_TestCase {

  /**
   * Set everything that is needed for the testing up.
   */
  public function setUp() {
  }

  /**
   * Test that empty arrays are decleared not valid.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testEmptyNotification() {

    $_GET = array();

    $this->notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $this->notification->validate();
  }

  /**
   * Test that incomplete arrays are decleared not valid.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testIncompleteNotification() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '5');

    $this->notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $this->notification->validate();
  }

  /**
   * Test function with invalid values.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testInvalidValuesNotification() {

   $_GET = array('state' => 'paid',
                 'transaction_id' => '<hack>',
                 'shop_id' => '10483',
                 'customer_email' => 'foo@bar.com',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen v.1.3.3.7',
                 'hash' => 'fb4393c37919371968f786a174b5a9b7340bc7e397fc480dd0d81e97873f87303c6799e855bc0a36d8673957bf00392b5b9a23f772660e67719534f13ac6d5c1'
                   );

    $this->notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $this->notification->validate();
  }

  /**
   * Test function with amount values.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testInvalidAmountNotification() {

   $_GET = array('state' => 'paid',
                 'transaction_id' => '2004.95',
                 'shop_id' => '10483',
                 'customer_email' => 'foo@bar.com',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen v.1.3.3.7',
                 'hash' => 'f37e091346df8f8a9dfed61772d62d1dae22bd30e159836fa1c01f21c4ce2933c0153fe66e8629601c695c3b28a6d61f20f1bfa3d66e54c362637b432e3dc265'
                   );

    $this->notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $this->notification->validate();
  }

  /**
   * Test function with invalid hash paid notification.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testInvalidHashPaidNotification() {

   $_GET = array('state' => 'paid',
                 'transaction_id' => '1',
                 'shop_id' => '10483',
                 'customer_email' => 'foo@bar.com',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen v.1.3.3.7',
                 'hash' => '85d13e7eda95276a655ef86947409f095be8ccd1736579d54a88fc9ce2ac5353964b33d8143439354ee46fa3ce0a7ea07c49429ae3bdbfeca4f2ab1990c15367'
                   );

    $this->notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $this->notification->validate();
  }

  /**
   * Test function with valid paid notification.
   */
  public function testValidPaidNotification() {

   $_GET = array('state' => 'paid',
                 'transaction_id' => '1',
                 'shop_id' => '10483',
                 'customer_email' => 'foo@bar.com',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen v.1.3.3.7',
                 'hash' => '85d13e7eda95276a655ef86947409f095be8ccd1736579d54a88fc9ce2ac5353964b33d8143439354ee46fa3ce0a7ea07c49429ae3bdbfeca4f2ab1990c15366'
                   );

    $this->notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $this->notification->validate();

    $this->assertTrue($this->notification->isValid());
    $this->assertEquals('payment', $this->notification->getNotificationType());
    $this->assertEquals('paid', $this->notification->getState());
    $this->assertEquals('1', $this->notification->getTransactionId());
    $this->assertEquals('10483', $this->notification->getShopId());
    $this->assertEquals('foo@bar.com', $this->notification->getCustomerEmail());
    $this->assertEquals('24.95', $this->notification->getAmount());
    $this->assertEquals('EUR', $this->notification->getCurrency());
    $this->assertEquals('1', $this->notification->getOrderId());
    $this->assertEquals('PHP SDK', $this->notification->getCustomVar0());
    $this->assertEquals('Euro 2012', $this->notification->getCustomVar1());
    $this->assertEquals('Barzahlen v.1.3.3.7', $this->notification->getCustomVar2());
    $this->assertEquals(array('PHP SDK', 'Euro 2012', 'Barzahlen v.1.3.3.7'), $this->notification->getCustomVar());

    unset($_GET['hash']);
    $this->assertEquals($_GET, $this->notification->getNotificationArray());
  }

  /**
   * Test function with valid refund notification.
   */
  public function testValidRefundNotification() {

   $_GET = array('state' => 'refund_completed',
                 'refund_transaction_id' => '1',
                 'origin_transaction_id' => '1',
                 'shop_id' => '10483',
                 'customer_email' => 'foo@bar.com',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'origin_order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen v.1.3.3.7',
                 'hash' => '55b3b182caf79881f5ac9a4fd7ac4f84824267fc8ac8a18dcfd25535b48a646eb28a0acf864faaff006365fd5f0480c09341930bf15dbcbe3ad27e4fa0d5c9f5'
                   );

    $this->notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $this->notification->validate();

    $this->assertTrue($this->notification->isValid());
    $this->assertEquals('refund', $this->notification->getNotificationType());
    $this->assertEquals('refund_completed', $this->notification->getState());
    $this->assertEquals('1', $this->notification->getRefundTransactionId());
    $this->assertEquals(null, $this->notification->getTransactionId());
    $this->assertEquals('1', $this->notification->getOriginTransactionId());
    $this->assertEquals('10483', $this->notification->getShopId());
    $this->assertEquals('foo@bar.com', $this->notification->getCustomerEmail());
    $this->assertEquals('24.95', $this->notification->getAmount());
    $this->assertEquals('EUR', $this->notification->getCurrency());
    $this->assertEquals(null, $this->notification->getOrderId());
    $this->assertEquals('1', $this->notification->getOriginOrderId());
    $this->assertEquals('PHP SDK', $this->notification->getCustomVar0());
    $this->assertEquals('Euro 2012', $this->notification->getCustomVar1());
    $this->assertEquals('Barzahlen v.1.3.3.7', $this->notification->getCustomVar2());
    $this->assertEquals(array('PHP SDK', 'Euro 2012', 'Barzahlen v.1.3.3.7'), $this->notification->getCustomVar());

    unset($_GET['hash']);
    $this->assertEquals($_GET, $this->notification->getNotificationArray());
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {

    unset($this->notification);
  }
}
?>