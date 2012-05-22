<?php
/**
 * Barzahlen Payment Module SDK
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

abstract class Barzahlen_Base {

  //const ApiDomain = 'https://api.barzahlen.com/v1/transactions/'; //!< call domain (productive use)
  //const ApiSandboxDomain = 'https://api-sandbox.barzahlen.com/v1/transactions/'; //!< sandbox call domain (productive use)
  const ApiDomain = 'https://dev-test.bar-zahlen.net:901/v1/transactions/'; //!< call domain (dev)
  const ApiSandboxDomain = 'https://api-online-sandbox.barzahlen.de:904/v1/transactions/'; //!< sandbox call domain (dev)

  const HashAlgo = 'sha512'; //!< hash algorithm
  const Separator = ';'; //!< separator character
  const MaxAttemps = 2; //!< maximum of allowed connection attempts

  /**
   * Generates the hash for the request array.
   *
   * @param array $requestArray array from which hash is requested
   * @param string $key private key depending on hash type
   * @return hash sum
   */
  final protected function _createHash(array $hashArray, $key) {

    $hashArray[] = $key;
    $hashString = implode(self::Separator, $hashArray);
    return hash(self::HashAlgo, $hashString);
  }
}
?>