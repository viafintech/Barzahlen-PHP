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

    $this->api = new Barzahlen_Api(SHOPID, PAYMENTKEY);
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