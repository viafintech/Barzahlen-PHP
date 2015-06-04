<?php
/**
 * Barzahlen Payment Module SDK
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     The MIT License (MIT) - http://opensource.org/licenses/MIT
 */

abstract class Barzahlen_Request_Base extends Barzahlen_Base
{
    protected $_isValid = false; //!< requests validity
    protected $_type; //!< request type
    protected $_xmlObj; //!< SimpleXMLElement
    protected $_xmlAttributes = array(); //!< expected xml nodes
    protected $_xmlData = array(); //!< array with parsed xml data

    abstract public function buildRequestArray($shopId, $paymentKey, $language);

    /**
     * Returns request type.
     *
     * @return type of request
     */
    public function getRequestType()
    {
        return $this->_type;
    }

    /**
     * Gets state of validity.
     *
     * @return boolean if request response is valid
     */
    public function isValid()
    {
        return $this->_isValid;
    }

    /**
     * Returns a single value from the xml array or the whole array.
     *
     * @param string $attribute single attribute, that shall be returned
     * @return single value if exists (else: null) or whole array
     */
    public function getXmlArray($attribute = '')
    {
        if ($attribute != '') {
            return array_key_exists($attribute, $this->_xmlData) ? $this->_xmlData[$attribute] : null;
        }

        return $this->_xmlData;
    }

    /**
     * Function to get response data out of received xml string.
     *
     * @param array $xmlResponse array with the response from the server
     * @param string $paymentKey merchants payment key
     * @return array with parsed xml data
     */
    public function parseXml($xmlResponse, $paymentKey)
    {
        if (!is_string($xmlResponse) || $xmlResponse == '') {
            throw new Barzahlen_Exception('No valid xml response received.');
        }

        try {
            $this->_xmlObj = new SimpleXMLElement($xmlResponse);
        } catch (Exception $e) {
            throw new Barzahlen_Exception($e->getMessage());
        }

        $this->_getXmlError();
        $this->_getXmlAttributes();
        $this->_checkXmlHash($paymentKey);
        $this->_isValid = true;
    }

    /**
     * Checks if an error occurred.
     */
    protected function _getXmlError()
    {
        if ($this->_xmlObj->{'result'} != 0) {
            throw new Barzahlen_Exception('XML response contains an error: ' . $this->_xmlObj->{'error-message'}, (int) $this->_xmlObj->{'result'});
        }
    }

    /**
     * Gets attributes from xml object depending on its type.
     *
     * @param string $responseType type for xml response
     */
    protected function _getXmlAttributes()
    {
        $this->_xmlData = array();

        foreach ($this->_xmlAttributes as $attribute) {
            $this->_xmlData[$attribute] = (string) $this->_xmlObj->{$attribute};
        }
    }

    /**
     * Checks if hash is valid.
     *
     * @param string $paymentKey merchants payment key
     */
    protected function _checkXmlHash($paymentKey)
    {
        $receivedHash = $this->_xmlData['hash'];
        unset($this->_xmlData['hash']);
        $generatedHash = $this->_createHash($this->_xmlData, $paymentKey);

        if ($receivedHash != $generatedHash) {
            throw new Barzahlen_Exception('response - xml hash not valid');
        }

        unset($this->_xmlData['result']);
    }
}
