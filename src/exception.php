<?php
/**
 * Barzahlen Payment Module SDK
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     The MIT License (MIT) - http://opensource.org/licenses/MIT
 */

class Barzahlen_Exception extends Exception
{
    /**
     * Constructor to create exception, uses parent function.
     *
     * @param string $message error message
     * @param integer $code error status code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * Output exception.
     *
     * @return string with error code and message
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}] - {$this->message}\n";
    }
}
