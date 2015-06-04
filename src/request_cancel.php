<?php
/**
 * Barzahlen Payment Module SDK
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     The MIT License (MIT) - http://opensource.org/licenses/MIT
 */

class Barzahlen_Request_Cancel extends Barzahlen_Request_Base
{
    protected $_type = 'cancel'; //!< request type
    protected $_transactionId; //!< transaction id
    protected $_xmlAttributes = array('transaction-id', 'result', 'hash'); //!< cancel xml content

    /**
     * Construtor to set variable request settings.
     *
     * @param string $transactionId transaction id
     */
    public function __construct($transactionId)
    {
        $this->_transactionId = $transactionId;
    }

    /**
     * Builds array for request.
     *
     * @param string $shopId merchants shop id
     * @param string $paymentKey merchants payment key
     * @param string $language langauge code (ISO 639-1)
     * @param array $customVar custom variables from merchant
     * @return array for update request
     */
    public function buildRequestArray($shopId, $paymentKey, $language)
    {
        $requestArray = array();
        $requestArray['shop_id'] = $shopId;
        $requestArray['transaction_id'] = $this->_transactionId;
        $requestArray['language'] = $language;
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
}
