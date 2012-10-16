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

class Barzahlen_Api extends Barzahlen_Base {

  protected $_shopId; //!< merchants shop id
  protected $_paymentKey; //!< merchants payment key
  protected $_customVar = array('', '', ''); //!< custom variables
  protected $_language = 'de'; //!< langauge code
  protected $_sandbox = false; //!< sandbox settings
  protected $_madeAttempts = 0; //!< performed attempts

  /**
   * Constructor. Sets basic settings.
   *
   * @param string $shopId merchants shop id
   * @param string $paymentKey merchants payment key
   * @param boolean $sandbox sandbox settings (default: false -> off)
   */
  public function __construct($shopId, $paymentKey, $sandbox = false) {

    $this->_shopId = $shopId;
    $this->_paymentKey = $paymentKey;
    $this->_sandbox = $sandbox;
  }

  /**
   * Lets the merchant sets custom variables.
   *
   * @param string $var0 First Custom Variable
   * @param string $var1 Second Custom Variable
   * @param string $var2 Third Custom Variable
   */
  public function setCustomVar($var0 = '', $var1 = '', $var2 = '') {

    $this->_customVar[0] = $var0;
    $this->_customVar[1] = $var1;
    $this->_customVar[2] = $var2;
  }

  /**
   * Sets the language for payment / refund slip.
   *
   * @param string $language Langauge Code (ISO 639-1)
   */
  public function setLanguage($language = 'de') {

    $this->_language = $language;
  }

  /**
   * Handles request of all kinds.
   *
   * @param Barzahlen_Request $request request that should be made
   */
  public function handleRequest($request) {

    $requestArray = $request->buildRequestArray($this->_shopId, $this->_paymentKey, $this->_language, $this->_customVar);
    $this->_debug("API: Sending request array to Barzahlen.", $requestArray);
    $xmlResponse = $this->_connectToApi($requestArray, $request->getRequestType());
    $this->_debug("API: Received XML response from Barzahlen.", $xmlResponse);
    $request->parseXml($xmlResponse, $this->_paymentKey);
    $this->_debug("API: Parsed XML response and returned it to request object.", $request->getXmlArray());
  }

  /**
   * Connects to Barzahlen Api as long as there's a xml response or maximum attempts are reached.
   *
   * @param array $requestArray array with the information which shall be send via POST
   * @param string $requestType type for request
   * @return xml response from Barzahlen
   */
  protected function _connectToApi(array $requestArray, $requestType) {

    $this->_madeAttempts++;

    try {
      return $this->_sendRequest($requestArray, $requestType);
    }
    catch (Exception $e) {
      if ($this->_madeAttempts >= self::MAXATTEMPTS) {
        throw new Barzahlen_Exception ($e->getMessage());
      }
      return $this->_connectToApi($requestArray, $requestType);
    }
  }

  /**
   * Send the information via HTTP POST to the given domain. A xml as anwser is expected.
   * SSL is required for a connection to Barzahlen.
   *
   * @param array $requestArray array with the information which shall be send via POST
   * @param string $requestType type of request
   * @return xml response from Barzahlen
   */
  protected function _sendRequest(array $requestArray, $requestType) {

    $callDomain = $this->_sandbox ? self::APIDOMAINSANDBOX.$requestType : self::APIDOMAIN.$requestType;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $callDomain);
    curl_setopt($ch, CURLOPT_POST, count($requestArray));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestArray);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/certs/ca-bundle.crt');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, 1.1);
    $return = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if($error != '') {
      throw new Barzahlen_Exception('Error during cURL: ' . $error);
    }

    return $return;
  }
}
?>