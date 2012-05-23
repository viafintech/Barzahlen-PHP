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

class Barzahlen_Request_Resend extends Barzahlen_Request_Base {

  protected $_type = 'resend_email'; //!< request type
  protected $_transactionId; //!< origin transaction id

  protected $_xmlAttributes = array('transaction-id', 'result', 'hash'); //!< resend xml content

  /**
   * Construtor to set variable request settings.
   *
   * @param string $transactionId origin transaction id
   */
  public function __construct($transactionId) {

    $this->_transactionId = $transactionId;
  }

  /**
   * Builds array for request.
   *
   * @param string $shopId merchants shop id
   * @param string $language langauge code (ISO 639-1)
   * @param array $customVar custom variables from merchant
   * @param string $paymentKey merchants payment key
   * @return array for resend request
   */
  public function buildRequestArray($shopId, $language, array $customVar, $paymentKey) {

    $requestArray = array();
    $requestArray['shop_id'] = $shopId;
    $requestArray['transaction_id'] = $this->_transactionId;
    $requestArray['language'] = $language;
    $requestArray['hash'] = $this->_createHash($requestArray, $paymentKey);

    return $requestArray;
  }

  /**
   * Returns transaction id from xml array.
   *
   * @return received transaction id
   */
  public function getTransactionId() {
    return $this->getXmlArray('transaction-id');
  }
}
?>