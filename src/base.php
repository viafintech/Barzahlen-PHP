<?php
/**
 * Barzahlen Payment Module SDK
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     The MIT License (MIT) - http://opensource.org/licenses/MIT
 */

abstract class Barzahlen_Base
{
    const APIDOMAIN = 'https://api.barzahlen.de/v1/transactions/'; //!< call domain (productive use)
    const APIDOMAINSANDBOX = 'https://api-sandbox.barzahlen.de/v1/transactions/'; //!< sandbox call domain
    const HASHALGO = 'sha512'; //!< hash algorithm
    const SEPARATOR = ';'; //!< separator character
    const MAXATTEMPTS = 2; //!< maximum of allowed connection attempts

    protected $_debug = false; //!< debug mode on / off
    protected $_logFile; //!< log file for debug output

    /**
     * Sets debug settings.
     *
     * @param boolean $debug debug mode on / off
     * @param string $logFile position of log file
     */
    public function setDebug($debug, $logFile)
    {
        $this->_debug = $debug;
        $this->_logFile = $logFile;
    }

    /**
     * Write debug message to log file.
     *
     * @param string $message debug message
     * @param array $data related data (optional)
     */
    protected function _debug($message, $data = array())
    {
        if ($this->_debug == true) {
            $time = date("[Y-m-d H:i:s] ");
            $message .= $data != array() ? " | " . serialize($data) : "";
            error_log($time . $message . "\r\r", 3, $this->_logFile);
        }
    }

    /**
     * Generates the hash for the request array.
     *
     * @param array $requestArray array from which hash is requested
     * @param string $key private key depending on hash type
     * @return hash sum
     */
    protected function _createHash(array $hashArray, $key)
    {
        $hashArray[] = $key;
        $hashString = implode(self::SEPARATOR, $hashArray);
        return hash(self::HASHALGO, $hashString);
    }

    /**
     * Removes empty values from arrays.
     *
     * @param array $array array with (empty) values
     */
    protected function _removeEmptyValues(array &$array)
    {
        foreach ($array as $key => $value) {
            if ($value == '') {
                unset($array[$key]);
            }
        }
    }

    /**
     * Converts ISO-8859-1 strings to UTF-8 if necessary.
     *
     * @param string $string text which is to check
     * @return string with utf-8 encoding
     */
    public function isoConvert($string)
    {
        if (!preg_match('/\S/u', $string)) {
            $string = utf8_encode($string);
        }

        return $string;
    }
}
