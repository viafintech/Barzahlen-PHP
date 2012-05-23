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

class ApiTest extends PHPUnit_Framework_TestCase {

  /**
   * Set everything that is needed for the testing up.
   */
  public function setUp() {

    $this->api = new Barzahlen_Api(SHOPID, PAYMENTKEY);
  }

  /**
   * This tests the existence of default values.
   */
  public function testAttributeExistanceAndDefaultValues() {

    $this->assertAttributeEquals(SHOPID, '_shopId', $this->api);
    $this->assertAttributeEquals(PAYMENTKEY, '_paymentKey', $this->api);
    $this->assertAttributeEquals(array('', '', ''), '_customVar', $this->api);
    $this->assertAttributeEquals('de', '_language', $this->api);
    $this->assertAttributeEquals(false, '_sandbox', $this->api);
    $this->assertAttributeEquals(0, '_madeAttempts', $this->api);
  }

  /**
   * Tests different custom variable settings.
   */
  public function testCustomVarSetter() {

    $this->api->setCustomVar('ABC', '{{}}');
    $this->assertAttributeEquals(array('ABC', '{{}}', ''), '_customVar', $this->api);

    $this->api->setCustomVar('Mein Shopsystem');
    $this->assertAttributeEquals(array('Mein Shopsystem', '', ''), '_customVar', $this->api);

    $this->api->setCustomVar('Mein Shopsystem', 'xt:Commerce', 'Magento');
    $this->assertAttributeEquals(array('Mein Shopsystem', 'xt:Commerce', 'Magento'), '_customVar', $this->api);

    $this->api->setCustomVar();
    $this->assertAttributeEquals(array('', '', ''), '_customVar', $this->api);
  }

  /**
   * Tests language setter with and without value.
   */
  public function testLanguageSetter() {

    $this->api->setLanguage('en');
    $this->assertAttributeEquals('en', '_language', $this->api);

    $this->api->setLanguage();
    $this->assertAttributeEquals('de', '_language', $this->api);
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {

    unset($this->api);
  }
}
?>
  