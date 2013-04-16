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

class Barzahlen_Request_Payment extends Barzahlen_Request_Base
{
    protected $_type = 'create'; //!< request type
    protected $_customerEmail; //!< customers e-mail address
    protected $_customerStreetNr; //!< customers street and street number
    protected $_customerZipcode; //!< customers zipcode
    protected $_customerCity; //!< customers city
    protected $_customerCountry; //!< customers country (ISO 3166-1 alpha-2)
    protected $_orderId; //!< order id
    protected $_amount; //!< payment amount
    protected $_currency; //!< currency of payment (ISO 4217)
    protected $_customVar = array('', '', ''); //!< custom variables
    protected $_xmlAttributes = array('transaction-id', 'payment-slip-link', 'expiration-notice',
        'infotext-1', 'infotext-2', 'result', 'hash'); //!< payment xml content

    /**
     * Construtor to set variable request settings.
     *
     * @param string $customerEmail customers e-mail address
     * @param string $customerStreetNr customers street and house number
     * @param string $customerZipcode customers zip code
     * @param string $customerCity customers city name
     * @param string $amount payment amount
     * @param string $currency currency of payment (ISO 4217)
     * @param string $orderId order id
     */
    public function __construct($customerEmail, $customerStreetNr, $customerZipcode, $customerCity, $customerCountry, $amount, $currency = 'EUR', $orderId = '')
    {
        $this->_customerEmail = $customerEmail;
        $this->_customerStreetNr = $this->isoConvert($customerStreetNr);
        $this->_customerZipcode = $customerZipcode;
        $this->_customerCity = $this->isoConvert($customerCity);
        $this->_customerCountry = $customerCountry;
        $this->_amount = round($amount, 2);
        $this->_currency = $currency;
        $this->_orderId = $orderId;
    }

    /**
     * Lets the merchant sets custom variables.
     *
     * @param string $var0 First Custom Variable
     * @param string $var1 Second Custom Variable
     * @param string $var2 Third Custom Variable
     */
    public function setCustomVar($var0 = '', $var1 = '', $var2 = '')
    {
        $this->_customVar[0] = $var0;
        $this->_customVar[1] = $var1;
        $this->_customVar[2] = $var2;
    }

    /**
     * Builds array for request.
     *
     * @param string $shopId merchants shop id
     * @param string $paymentKey merchants payment key
     * @param string $language langauge code (ISO 639-1)
     * @param array $customVar custom variables from merchant
     * @return array for payment request
     */
    public function buildRequestArray($shopId, $paymentKey, $language)
    {
        $requestArray = array();
        $requestArray['shop_id'] = $shopId;
        $requestArray['customer_email'] = $this->_customerEmail;
        $requestArray['amount'] = $this->_amount;
        $requestArray['currency'] = $this->_currency;
        $requestArray['language'] = $language;
        $requestArray['order_id'] = $this->_orderId;
        $requestArray['customer_street_nr'] = $this->_customerStreetNr;
        $requestArray['customer_zipcode'] = $this->_customerZipcode;
        $requestArray['customer_city'] = $this->_customerCity;
        $requestArray['customer_country'] = $this->_customerCountry;
        $requestArray['custom_var_0'] = $this->_customVar[0];
        $requestArray['custom_var_1'] = $this->_customVar[1];
        $requestArray['custom_var_2'] = $this->_customVar[2];
        $requestArray['hash'] = $this->_createHash($requestArray, $paymentKey);

        $this->_removeEmptyValues($requestArray);
        return $requestArray;
    }

    /**
     * Returns transaction id from xml array.
     *
     * @return received transaction id
     */
    public function getTransactionId()
    {
        return $this->getXmlArray('transaction-id');
    }

    /**
     * Returns payment slip link from xml array.
     *
     * @return received payment slip link
     */
    public function getPaymentSlipLink()
    {
        return $this->getXmlArray('payment-slip-link');
    }

    /**
     * Returns expiration notice from xml array.
     *
     * @return received expiration notice
     */
    public function getExpirationNotice()
    {
        return $this->getXmlArray('expiration-notice');
    }

    /**
     * Returns infotext 1 from xml array.
     *
     * @return received infotext 1
     */
    public function getInfotext1()
    {
        return $this->getXmlArray('infotext-1');
    }

    /**
     * Returns infotext 2 from xml array.
     *
     * @return received infotext 2
     */
    public function getInfotext2()
    {
        return $this->getXmlArray('infotext-2');
    }
}
