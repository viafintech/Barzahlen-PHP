<?php

namespace Barzahlen\Exception;

use Barzahlen\Translate;

class ApiException extends \Exception
{
    const LOGFILE = 'barzahlen_php_sdk.log';

    /**
     * @var string
     */
    protected $requestId;


    /**
     * @param string $message
     * @param string $requestId
     * @param array $aParams
     * @param bool $bLog
     */
    public function __construct($message, $requestId = 'N/A', $aParams = array(), $bLog = false)
    {
        $message = Translate::__T($message, $aParams);
        if($bLog)
            $this->log($message);

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

    /**
     * logs string to file
     * @param $sString
     */
    private function log($sString)
    {
        try {
            if (file_exists(self::LOGFILE) && time()-filemtime(self::LOGFILE) > 24 * 3600) {
                file_put_contents(self::LOGFILE, '');
            }

            $oDate = new \DateTime();
            file_put_contents(self::LOGFILE, $oDate->format('Y-m-d H:i:s') . ' - ' . $sString, FILE_APPEND);
        } catch(\Exception $e) {
            trigger_error($e->getMessage());
        }
    }
}
