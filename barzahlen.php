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

class barzahlen {

  const HashAlgo = 'sha512'; //!< hash algorithm
  const Separator = ';'; //!< separator character

  protected $_shopId; //!< merchants shop id
  protected $_paymentKey; //!< merchants payment key
  protected $_notificationKey; //!< merchants notification key
  protected $_customVar = array('', '', ''); //!< custom variables
  protected $_language = 'de'; //!< langauge code
  protected $_sandbox = false; //!< sandbox settings

  /**
   * Constructor. Sets basic settings.
   *
   * @param string $shopId Barzahlen Shop ID
   * @param string $paymentKey Personal Payment Key
   * @param string $notificationKey Personal Notification Key
   * @param boolean $sandbox Sandbox On / Off (default: false -> Off)
   */
  public function __construct($shopId, $paymentKey, $notificationKey, $sandbox = false) {

    $this->_shopId = $shopId;
    $this->_paymentKey = $paymentKey;
    $this->_notificationKey = $notificationKey;
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

    $this->_language = $langauge;
  }

  /**
   * Create payment and receive transaction id as well as payment slip.
   *
   * @param string $customerEmail Customers E-mail Address
   * @param string $orderId Order ID
   * @param string $amount Payment Amount
   * @param string $currency Currency of Payment (ISO 4217)
   * @return
   */
  public function create($customerEmail, $orderId, $amount, $currency = 'EUR') {

    $transactionArray = $this->_createTransactionArray($customerEmail, $orderId, $amount, $currency);
    $xmlResponse = $this->_sendRequest($transactionArray, 'create', $this->_sandbox);
    return $this->_parseResponse($xmlResponse);
  }

  /**
   * Combines all information to the create request array.
   *
   * @param string $customerEmail Customers E-mail Address
   * @param string $orderId Order ID
   * @param string $amount Payment Amount
   * @param string $currency Currency of Payment (ISO 4217)
   * @return array for create request
   */
  protected function _createTransactionArray($customerEmail, $orderId, $amount, $currency) {

    $transactionArray = array();
    $transactionArray['shop_id'] = $this->_shopId;
    $transactionArray['customer_email'] = $customerEmail;
    $transactionArray['amount'] = $amount;
    $transactionArray['currency'] = $currency;
    $transactionArray['language'] = $this->_language;
    $transactionArray['order_id'] = $orderId;
    $transactionArray['custom_var_0'] = $this->_customVar[0];
    $transactionArray['custom_var_1'] = $this->_customVar[1];
    $transactionArray['custom_var_2'] = $this->_customVar[2];
    $transactionArray['hash'] = $this->_createHash($transactionArray);

    return $transactionArray;
  }

  /**
   * Request refund for a specific order / transaction.
   *
   * @param string $transactionId Origin Transaction ID
   * @param string $amount Refund Amount
   * @param string $currency Currency of Refund (ISO 4217)
   * @return
   */
  public function refund($transactionId, $amount, $currency = 'EUR') {

    $refundArray = $this->_createRefundArray($transactionId, $amount, $currency);
    $xmlResponse = $this->_sendRequest($refundArray, 'refund', $this->_sandbox);
    return $this->_parseResponse($xmlResponse);
  }

  /**
   * Combines all information to the refund request array.
   *
   * @param string $transactionId Origin Transaction ID
   * @param string $amount Refund Amount
   * @param string $currency Currency of Refund (ISO 4217)
   * @return array for refund request
   */
  protected function _createRefundArray($transactionId, $amount, $currency) {

    $refundArray = array();
    $refundArray['shop_id'] = $this->_shopId;
    $refundArray['transaction_id'] = $transactionId;
    $refundArray['amount'] = $amount;
    $refundArray['currency'] = $currency;
    $refundArray['language'] = $this->_language;
    $refundArray['hash'] = $this->_createHash($refundArray);

    return $refundArray;
  }

  /**
   * Resend payment / refund slip to customers e-mail address.
   *
   * @param string $transactionId Origin Transaction ID
   * @return
   */
  public function resend($transactionId) {

    $resendArray = $this->_createResendArray($transactionId);
    $xmlResponse = $this->_sendRequest($resendArray, 'refund_email', $this->_sandbox);
    return $this->_parseResponse($xmlResponse);
  }

  /**
   * Combines all information to the resend request array.
   *
   * @param string $transactionId Origin Transaction ID
   * @return array for resend request
   */
  protected function _createResendArray($transactionId) {

    $resendArray = array();
    $resendArray['shop_id'] = $this->_shopId;
    $resendArray['transaction_id'] = $transactionId;
    $resendArray['language'] = $this->_language;
    $resendArray['hash'] = $this->_createHash($resendArray);

    return $resendArray;
  }

  /**
   * Generates the hash for the request array.
   *
   * @param array $requestArray Create / Refund / Resend Array
   * @return hash sum
   */
  protected function _createHash(array $requestArray) {

    $requestArray[] = $this->_paymentKey;
    $hashString = implode(Separator, $requestArray);
    return hash(HashAlgo, $hashString);
  }
}

?>