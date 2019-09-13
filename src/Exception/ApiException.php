<?php

namespace Barzahlen\Exception;

class ApiException extends \Exception
{
    /**
     * @var string
     */
    protected $requestId;


    /**
     * @param string $message
     * @param string $requestId
     */
    public function __construct($message, $requestId = 'N/A', $aParams = array())
    {
        parent::__construct($message);
        $this->requestId = $requestId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": {$this->message} - RequestId: {$this->requestId}";
    }
}
