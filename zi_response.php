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

class BZ_SDK_Response {
  
  protected $_xmlObj; //!< SimpleXMLElement
  protected $_responseArray; //!< array with parsed data

  protected $_createXml = array('transaction-id', 'payment-slip-link', 'expiration-notice',
                                'infotext-1', 'infotext-2', 'result', 'hash'); //!< create xml content

  protected $_refundXml = array('origin-transaction-id', 'refund-transaction-id', 'result', 'hash'); //!< refund xml content
  
  protected $_resendXml = array('transaction-id', 'result', 'hash'); //!< resend xml content
  
  /**
   * Function to get response data out of received xml string.
   *
   * @param array $xmlResponse array with the response from the server
   * @param string $responseType type of the request that led to response
   * @return array with parsed xml data
   */
  protected function _parseResponse($xmlResponse, $responseType) {

    if(!is_string($xmlResponse) || $xmlResponse == '') {
      throw new Exception('No valid xml response received.');
    }

    $this->_xmlObj = new SimpleXMLElement($xmlResponse);

    $this->_checkForError();
    $this->_getAttributes($responseType);
    $this->_checkHash();
    
    return $this->_responseArray;
  }

  /**
   * Checks if an error occurred.
   */
  protected function _checkForError() {

    if($this->_xmlObj->{'result'} != 0) {
      throw new Exception('XML response contains an error: ' . $this->_xmlObj->{'error-message'});
    }
  }

  /**
   * Gets attributes from xml object depending on its type.
   *
   * @param string $responseType type for xml response
   */
  protected function _getAttributes($responseType) {
    
    $this->_responseArray = array();
    
    switch ($responseType) {
      case 'create':
        $xmlArray = $this->_createXml;
        break;
      case 'refund':
        $xmlArray = $this->_refundXml;
        break;
      case 'resend':
        $xmlArray = $this->_resendXml;
        break;
      default:
        throw new Exception('response - unable to handle response type');
        break;
    }
    
    foreach ($xmlArray as $attribute) {
      $this->_responseArray[$attribute] = (string)$this->_xmlObj->{$attribute};
    }
  }

  /**
   * Checks if hash is valid.
   */
  protected function _checkHash() {

    $receivedHash = $this->_responseArray['hash'];
    unset($this->_responseArray['hash']);
    $generatedHash = $this->_createHash($this->_responseArray);

    if($receivedHash != $generatedHash){
      throw new Exception('response - xml hash not valid');
    }
    
    unset($this->_responseArray['result']);
  }
}
?>