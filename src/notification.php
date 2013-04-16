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

class Barzahlen_Notification extends Barzahlen_Base
{
    protected $_isValid = false; //!< state of validity
    protected $_shopId; //!< merchants shop id
    protected $_notificationKey; //!< merchants notification key
    protected $_receivedData; //!< data which were send by Barzahlen
    protected $_notificationType = 'payment'; //!< type of notification (payment or refund)
    protected $_notficationData = array('state', 'transaction_id', 'shop_id', 'customer_email', 'amount',
        'currency', 'hash'); //!< all necessary attributes for a valid notification
    protected $_originData = array('transaction_id', 'order_id'); //!< origin values for refund notifications

    /**
     * Constructor. Sets basic settings.
     *
     * @param string $shopId merchants shop id
     * @param string $notificationKey merchants notification key
     */
    public function __construct($shopId, $notificationKey, array $receivedData)
    {
        $this->_shopId = $shopId;
        $this->_notificationKey = $notificationKey;
        $this->_receivedData = $receivedData;
    }

    /**
     * Validates the received data. Throws exception when an error occurrs.
     */
    public function validate()
    {
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
    public function isValid()
    {
        return $this->_isValid;
    }

    /**
     * Checks that all attributes are available.
     */
    protected function _checkExistence()
    {
        if (array_key_exists('refund_transaction_id', $this->_receivedData)) {
            $this->_notificationType = 'refund';
            foreach ($this->_originData as $attribute) {
                $this->_notficationData = str_replace($attribute, 'origin_' . $attribute, $this->_notficationData);
                $this->_notficationData[] = 'refund_transaction_id';
            }
        }

        foreach ($this->_notficationData as $attribute) {
            if (!array_key_exists($attribute, $this->_receivedData)) {
                throw new Barzahlen_Exception('Notification array not complete, at least ' . $attribute . ' is missing.');
            }
        }
    }

    /**
     * Checks that attribute values are as expected.
     */
    protected function _checkValues()
    {
        if ($this->_notificationType == 'refund') {
            if (!is_numeric($this->_receivedData['refund_transaction_id'])) {
                throw new Barzahlen_Exception('Refund transaction id is not numeric.');
            }
            if (!is_numeric($this->_receivedData['origin_transaction_id'])) {
                throw new Barzahlen_Exception('Origin transaction id is not numeric.');
            }
        } else {
            if (!is_numeric($this->_receivedData['transaction_id'])) {
                throw new Barzahlen_Exception('Transaction id is not numeric.');
            }
        }

        if ($this->_shopId != $this->_receivedData['shop_id']) {
            throw new Barzahlen_Exception('Shop id doesn\'t match the given value.');
        }

        if (!preg_match('/^\d{1,3}(\.\d\d?)?$/', $this->_receivedData['amount'])) {
            throw new Barzahlen_Exception('Amount is no valid value.');
        }
    }

    /**
     * Checks that received hash is valid.
     */
    protected function _checkHash()
    {
        $receivedHash = $this->_receivedData['hash'];
        $hashArray = $this->_sortAttributes();
        $generatedHash = $this->_createHash($hashArray, $this->_notificationKey);

        if ($receivedHash != $generatedHash) {
            throw new Barzahlen_Exception('Notification hash is not valid.');
        }
    }

    /**
     * Puts $_GET attributes in the right order.
     *
     * @return array for hash generation
     */
    protected function _sortAttributes()
    {
        $hashArray = array();
        $hashArray[] = $this->_receivedData['state'];
        if ($this->_notificationType == 'refund') {
            $hashArray[] = $this->_receivedData['refund_transaction_id'];
            $hashArray[] = $this->_receivedData['origin_transaction_id'];
        } else {
            $hashArray[] = $this->_receivedData['transaction_id'];
        }
        $hashArray[] = $this->_receivedData['shop_id'];
        $hashArray[] = $this->_receivedData['customer_email'];
        $hashArray[] = $this->_receivedData['amount'];
        $hashArray[] = $this->_receivedData['currency'];
        if ($this->_notificationType == 'refund') {
            $hashArray[] = isset($this->_receivedData['origin_order_id']) ? $this->_receivedData['origin_order_id'] : '';
        } else {
            $hashArray[] = isset($this->_receivedData['order_id']) ? $this->_receivedData['order_id'] : '';
        }
        $hashArray[] = isset($this->_receivedData['custom_var_0']) ? $this->_receivedData['custom_var_0'] : '';
        $hashArray[] = isset($this->_receivedData['custom_var_1']) ? $this->_receivedData['custom_var_1'] : '';
        $hashArray[] = isset($this->_receivedData['custom_var_2']) ? $this->_receivedData['custom_var_2'] : '';

        return $hashArray;
    }

    /**
     * Returns a single value from the notification array or the whole array.
     *
     * @param string $attribute single attribute, that shall be returned
     * @return single value if exists (else: null) or whole array
     */
    public function getNotificationArray($attribute = '')
    {
        if (!$this->_isValid) {
            return null;
        }

        if ($attribute != '') {
            return array_key_exists($attribute, $this->_receivedData) ? $this->_receivedData[$attribute] : null;
        }

        return $this->_receivedData;
    }

    /**
     * Returns notification type.
     *
     * @return string with notification type
     */
    public function getNotificationType()
    {
        return $this->_isValid ? $this->_notificationType : null;
    }

    /**
     * Returns notification state.
     *
     * @return string with state
     */
    public function getState()
    {
        return $this->getNotificationArray('state');
    }

    /**
     * Returns refund transaction id.
     *
     * @return string with refund transaction id
     */
    public function getRefundTransactionId()
    {
        return $this->getNotificationArray('refund_transaction_id');
    }

    /**
     * Returns transaction id.
     *
     * @return string with transaction id
     */
    public function getTransactionId()
    {
        return $this->getNotificationArray('transaction_id');
    }

    /**
     * Returns origin transaction id.
     *
     * @return string with origin transaction id
     */
    public function getOriginTransactionId()
    {
        return $this->getNotificationArray('origin_transaction_id');
    }

    /**
     * Returns shop id.
     *
     * @return string with shop id
     */
    public function getShopId()
    {
        return $this->getNotificationArray('shop_id');
    }

    /**
     * Returns customer e-mail.
     *
     * @return string with customer e-mail
     */
    public function getCustomerEmail()
    {
        return $this->getNotificationArray('customer_email');
    }

    /**
     * Returns amount.
     *
     * @return string with amount
     */
    public function getAmount()
    {
        return $this->getNotificationArray('amount');
    }

    /**
     * Returns currency.
     *
     * @return string with currency
     */
    public function getCurrency()
    {
        return $this->getNotificationArray('currency');
    }

    /**
     * Returns order id.
     *
     * @return string with order id
     */
    public function getOrderId()
    {
        return $this->getNotificationArray('order_id');
    }

    /**
     * Returns origin order id.
     *
     * @return string with origin order id
     */
    public function getOriginOrderId()
    {
        return $this->getNotificationArray('origin_order_id');
    }

    /**
     * Returns customer var 0.
     *
     * @return string with custom var
     */
    public function getCustomVar0()
    {
        return $this->getNotificationArray('custom_var_0');
    }

    /**
     * Returns customer var 1.
     *
     * @return string with custom var
     */
    public function getCustomVar1()
    {
        return $this->getNotificationArray('custom_var_1');
    }

    /**
     * Returns customer var 2.
     *
     * @return string with custom var
     */
    public function getCustomVar2()
    {
        return $this->getNotificationArray('custom_var_2');
    }

    /**
     * Returns customer var as array.
     *
     * @return array with custom variables
     */
    public function getCustomVar()
    {
        return array($this->getCustomVar0(), $this->getCustomVar1(), $this->getCustomVar2());
    }
}
