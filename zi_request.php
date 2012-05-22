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

require_once('zi_response.php');

class BZ_SDK_Request extends BZ_SDK_Response {
  
  //const ApiDomain = 'https://api.barzahlen.com/v1/transactions/'; //!< call domain (productive use)
  //const ApiSandboxDomain = 'https://api-sandbox.barzahlen.com/v1/transactions/'; //!< sandbox call domain (productive use)
  const ApiDomain = 'https://dev-test.bar-zahlen.net:901/v1/transactions/'; //!< call domain (dev)
  const ApiSandboxDomain = 'https://api-online-sandbox.barzahlen.de:904/v1/transactions/'; //!< sandbox call domain (dev)
  
  /**
   * Send the information via HTTP POST to the given domain. A xml as anwser is expected. 
   * SSL is required for a connection to Barzahlen.
   *
   * @param array $requestArray array with the information which shall be send via POST
   * @param string $requestType type of request
   * @param boolean $sandbox Sandbox settings (On / Off)
   * @return xml response from Barzahlen
   */
  protected function _sendRequest(array $requestArray, $requestType, $sandbox) {

    $callDomain = $sandbox ? self::ApiSandboxDomain.$requestType : self::ApiDomain.$requestType;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $callDomain);
    curl_setopt($ch, CURLOPT_POST, count($requestArray));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestArray);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_CAINFO, 'certs/barzahlen_ca.pem');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, 1.1);
    $return = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if($error != '') {
      throw new Exception('Error during cURL: ' . $error);
    }

    return $return;
  }
}
?>