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
}
?>