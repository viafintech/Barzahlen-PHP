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

abstract class Barzahlen_Request_Base extends Barzahlen_Base {

  protected $_isValid = false; //!< requests validity
  protected $_type; //!< request type
  protected $_xmlObj; //!< SimpleXMLElement
  protected $_xmlAttributes = array(); //!< expected xml nodes
  protected $_xmlData = array(); //!< array with parsed xml data

  abstract public function __construct();
  abstract public function buildRequestArray($shopId, $language, array $customVar, $paymentKey);

  /**
   * Returns request type.
   *
   * @return type of request
   */
  final public function getRequestType() {
    return $this->_type;
  }

  /**
   * Gets state of validity.
   *
   * @return boolean if request response is valid
   */
  final public function isValid() {
    return $this->_isValid;
  }

  /**
   * Returns a single value from the xml array or the whole array.
   *
   * @param string $attribute single attribute, that shall be returned
   * @return single value if exists (else: null) or whole array
   */
  final public function getXmlArray($attribute = '') {

    if($attribute != '') {
      return array_key_exists($attribute, $this->_xmlData) ? $this->_xmlData[$attribute] : null;
    }

    return $this->_xmlData;
  }

  /**
   * Function to get response data out of received xml string.
   *
   * @param array $xmlResponse array with the response from the server
   * @param string $paymentKey merchants payment key
   * @return array with parsed xml data
   */
  final public function parseXml($xmlResponse, $paymentKey) {

    if(!is_string($xmlResponse) || $xmlResponse == '') {
      throw new Barzahlen_Exception('No valid xml response received.');
    }

    $this->_xmlObj = new SimpleXMLElement($xmlResponse);

    $this->_getXmlError();
    $this->_getXmlAttributes();
    $this->_checkXmlHash($paymentKey);
    $this->_isValid = true;
  }

  /**
   * Checks if an error occurred.
   */
  final protected function _getXmlError() {

    if($this->_xmlObj->{'result'} != 0) {
      throw new Barzahlen_Exception('XML response contains an error: ' . $this->_xmlObj->{'error-message'}, (int)$this->_xmlObj->{'result'});
    }
  }

  /**
   * Gets attributes from xml object depending on its type.
   *
   * @param string $responseType type for xml response
   */
  final protected function _getXmlAttributes() {

    $this->_xmlData = array();

    foreach ($this->_xmlAttributes as $attribute) {
      $this->_xmlData[$attribute] = (string)$this->_xmlObj->{$attribute};
    }
  }

  /**
   * Checks if hash is valid.
   *
   * @param string $paymentKey merchants payment key
   */
  final protected function _checkXmlHash($paymentKey) {

    $receivedHash = $this->_xmlData['hash'];
    unset($this->_xmlData['hash']);
    $generatedHash = $this->_createHash($this->_xmlData, $paymentKey);

    if($receivedHash != $generatedHash){
      throw new Barzahlen_Exception('response - xml hash not valid');
    }

    unset($this->_xmlData['result']);
  }
}
?>