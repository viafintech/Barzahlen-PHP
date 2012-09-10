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
 * @author      Alexander Diebler (alexander.diebler@barzahlen.de)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class Barzahlen_Request_Payment extends Barzahlen_Request_Base {

  protected $_type = 'create'; //!< request type
  protected $_customerEmail; //!< customers e-mail address
  protected $_orderId; //!< order id
  protected $_amount; //!< payment amount
  protected $_currency; //!< currency of payment (ISO 4217)

  protected $_xmlAttributes = array('transaction-id', 'payment-slip-link', 'expiration-notice',
                                    'infotext-1', 'infotext-2', 'result', 'hash'); //!< payment xml content

  /**
   * Construtor to set variable request settings.
   *
   * @param string $customerEmail customers e-mail address
   * @param string $orderId order id
   * @param string $amount payment amount
   * @param string $currency currency of payment (ISO 4217)
   * @return
   */
  public function __construct($customerEmail, $orderId, $amount, $currency = 'EUR') {

    $this->_customerEmail = $customerEmail;
    $this->_orderId = $orderId;
    $this->_amount = $amount;
    $this->_currency = $currency;
  }

  /**
   * Builds array for request.
   *
   * @param string $shopId merchants shop id
   * @param string $language langauge code (ISO 639-1)
   * @param array $customVar custom variables from merchant
   * @param string $paymentKey merchants payment key
   * @return array for payment request
   */
  public function buildRequestArray($shopId, $language, array $customVar, $paymentKey) {

    $requestArray = array();
    $requestArray['shop_id'] = $shopId;
    $requestArray['customer_email'] = $this->_customerEmail;
    $requestArray['amount'] = $this->_amount;
    $requestArray['currency'] = $this->_currency;
    $requestArray['language'] = $language;
    $requestArray['order_id'] = $this->_orderId;
    $requestArray['custom_var_0'] = $customVar[0];
    $requestArray['custom_var_1'] = $customVar[1];
    $requestArray['custom_var_2'] = $customVar[2];
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

  /**
   * Returns payment slip link from xml array.
   *
   * @return received payment slip link
   */
  public function getPaymentSlipLink() {
    return $this->getXmlArray('payment-slip-link');
  }

  /**
   * Returns expiration notice from xml array.
   *
   * @return received expiration notice
   */
  public function getExpirationNotice() {
    return $this->getXmlArray('expiration-notice');
  }

  /**
   * Returns infotext 1 from xml array.
   *
   * @return received infotext 1
   */
  public function getInfotext1() {
    return $this->getXmlArray('infotext-1');
  }

  /**
   * Returns infotext 2 from xml array.
   *
   * @return received infotext 2
   */
  public function getInfotext2() {
    return $this->getXmlArray('infotext-2');
  }
}
?>