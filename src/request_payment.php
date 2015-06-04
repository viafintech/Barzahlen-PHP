<?php
/**
 * Barzahlen Payment Module SDK
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     The MIT License (MIT) - http://opensource.org/licenses/MIT
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
    protected $_dueDate; //!< due date for payment (ISO 8601)
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
     * @param string $dueDate due date for payment slip (ISO 8601)
     */
    public function __construct($customerEmail, $customerStreetNr, $customerZipcode, $customerCity, $customerCountry, $amount, $currency = 'EUR', $orderId = '', $dueDate = null)
    {
        $this->_customerEmail = $this->isoConvert($customerEmail);
        $this->_customerStreetNr = $this->isoConvert($customerStreetNr);
        $this->_customerZipcode = $customerZipcode;
        $this->_customerCity = $this->isoConvert($customerCity);
        $this->_customerCountry = $customerCountry;
        $this->_amount = number_format($amount, 2, '.', '');
        $this->_currency = $currency;
        $this->_orderId = $orderId;
        $this->_dueDate = $dueDate;
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
        $this->_customVar[0] = $this->isoConvert($var0);
        $this->_customVar[1] = $this->isoConvert($var1);
        $this->_customVar[2] = $this->isoConvert($var2);
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
        $requestArray['due_date'] = $this->_dueDate;

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
