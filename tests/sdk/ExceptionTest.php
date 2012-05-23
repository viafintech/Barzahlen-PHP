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

class ExceptionTest extends PHPUnit_Framework_TestCase {

  /**
   * Set everything that is needed for the testing up.
   */
  public function setUp() {
  }

  /**
   * This tests the existence of default values.
   */
  public function testAttributeExistanceAndDefaultValues() {

    $exception = new Barzahlen_Exception('An error occurred.', 42);
    $this->assertEquals("Barzahlen_Exception: [42] - An error occurred.\n", $exception->__toString());
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {
  }
}
?>