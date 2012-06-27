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

class Barzahlen_Notification extends Barzahlen_Base {

  protected $_isValid = false; //!< state of validity
  protected $_shopId; //!< merchants shop id
  protected $_notificationKey; //!< merchants notification key
  protected $_receivedData; //!< data which were send by Barzahlen
  protected $_notificationType = 'payment'; //!< type of notification (payment or refund)

  protected $_notficationData = array('state','transaction_id','shop_id','customer_email','amount',
                                      'currency','order_id','custom_var_0','custom_var_1',
                                      'custom_var_2','hash'); //!< all necessary attributes for a valid notification

  protected $_originData = array('transaction_id','order_id'); //!< numeric values for database queries

  /**
   * Constructor. Sets basic settings.
   *
   * @param string $shopId merchants shop id
   * @param string $notificationKey merchants notification key
   */
  public function __construct($shopId, $notificationKey, array $receivedData) {

    $this->_shopId = $shopId;
    $this->_notificationKey = $notificationKey;
    $this->_receivedData = $receivedData;
  }

  /**
   * Validates the received data. Throws exception when an error occurrs.
   */
  public function validate() {

    $this->_checkExistence();
    $this->_checkValues();
    $this->_checkHash();
    $this->_isValid = true;
  }

  /**
   * Gets state of validity.
   *
   * @return boolean if notification is valid
   */
  public function isValid() {
    return $this->_isValid;
  }

  /**
   * Checks that all attributes are available.
   */
  protected function _checkExistence() {

    if(array_key_exists('refund_transaction_id', $this->_receivedData)) {
      $this->_notificationType = 'refund';
      foreach ($this->_originData as $attribute) {
        $this->_notficationData = str_replace($attribute, 'origin_' . $attribute, $this->_notficationData);
        $this->_notficationData[] = 'refund_transaction_id';
      }
    }

    foreach ($this->_notficationData as $attribute) {
      if(!array_key_exists($attribute, $this->_receivedData)) {
        throw new Barzahlen_Exception('Notification array not complete, at least '. $attribute .' is missing.');
      }
    }
  }

  /**
   * Checks that all numeric attributes are numeric.
   */
  protected function _checkValues() {

    $invalid = 0;

    if($this->_notificationType == 'refund') {
      $invalid += !is_numeric($this->_receivedData['refund_transaction_id']);
      $invalid += !is_numeric($this->_receivedData['origin_transaction_id']);
    }
    else {
      $invalid += !is_numeric($this->_receivedData['transaction_id']);
    }

    $invalid += $this->_shopId != $this->_receivedData['shop_id'];
    $invalid += !preg_match('/^(1000(\.00?)?|\d{1,3}(\.\d\d?)?)$/', $this->_receivedData['amount']);

    if($invalid > 0) {
      throw new Barzahlen_Exception('Notification contains '. $invalid .' invalid values.');
    }
  }

  /**
   * Checks that received hash is valid.
   */
  protected function _checkHash() {

    $receivedHash = $this->_receivedData['hash'];
    $this->_cleanNotNecessaryAttributes();
    $generatedHash = $this->_createHash($this->_receivedData, $this->_notificationKey);

    if($receivedHash != $generatedHash){
      throw new Barzahlen_Exception('Notification hash is not valid.');
    }
  }

  /**
   * Gets rid of additional $_GET attributes.
   */
  protected function _cleanNotNecessaryAttributes() {

    foreach ($this->_receivedData as $attribute => $value) {
      if(!in_array($attribute, $this->_notficationData)) {
        unset($this->_receivedData[$attribute]);
      }
    }
    unset($this->_receivedData['hash']);
  }

  /**
   * Returns a single value from the notification array or the whole array.
   *
   * @param string $attribute single attribute, that shall be returned
   * @return single value if exists (else: null) or whole array
   */
  public function getNotificationArray($attribute = '') {

    if($attribute != '') {
      return array_key_exists($attribute, $this->_receivedData) && $this->_isValid ? $this->_receivedData[$attribute] : null;
    }

    return $this->_isValid ? $this->_receivedData : null;
  }

  /**
   * Returns notification type.
   *
   * @return string with notification type
   */
  public function getNotificationType() {
    return $this->_isValid ? $this->_notificationType : null;
  }

  /**
   * Returns notification state.
   *
   * @return string with state
   */
  public function getState() {
    return $this->getNotificationArray('state');
  }

  /**
   * Returns refund transaction id.
   *
   * @return string with refund transaction id
   */
  public function getRefundTransactionId() {
    return $this->getNotificationArray('refund_transaction_id');
  }

  /**
   * Returns transaction id.
   *
   * @return string with transaction id
   */
  public function getTransactionId() {
    return $this->getNotificationArray('transaction_id');
  }

  /**
   * Returns origin transaction id.
   *
   * @return string with origin transaction id
   */
  public function getOriginTransactionId() {
    return $this->getNotificationArray('origin_transaction_id');
  }

  /**
   * Returns shop id.
   *
   * @return string with shop id
   */
  public function getShopId() {
    return $this->getNotificationArray('shop_id');
  }

  /**
   * Returns customer e-mail.
   *
   * @return string with customer e-mail
   */
  public function getCustomerEmail() {
    return $this->getNotificationArray('customer_email');
  }

  /**
   * Returns amount.
   *
   * @return string with amount
   */
  public function getAmount() {
    return $this->getNotificationArray('amount');
  }

  /**
   * Returns currency.
   *
   * @return string with currency
   */
  public function getCurrency() {
    return $this->getNotificationArray('currency');
  }

  /**
   * Returns order id.
   *
   * @return string with order id
   */
  public function getOrderId() {
    return $this->getNotificationArray('order_id');
  }

  /**
   * Returns origin order id.
   *
   * @return string with origin order id
   */
  public function getOriginOrderId() {
    return $this->getNotificationArray('origin_order_id');
  }

  /**
   * Returns customer var 0.
   *
   * @return string with custom var
   */
  public function getCustomVar0() {
    return $this->getNotificationArray('custom_var_0');
  }

  /**
   * Returns customer var 1.
   *
   * @return string with custom var
   */
  public function getCustomVar1() {
    return $this->getNotificationArray('custom_var_1');
  }

  /**
   * Returns customer var 2.
   *
   * @return string with custom var
   */
  public function getCustomVar2() {
    return $this->getNotificationArray('custom_var_2');
  }

  /**
   * Returns customer var as array.
   *
   * @return array with custom variables
   */
  public function getCustomVar() {
    return array($this->getCustomVar0(), $this->getCustomVar1(), $this->getCustomVar2());
  }
}
?>