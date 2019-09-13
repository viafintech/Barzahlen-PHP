<?php
namespace Barzahlen\Request;

use Barzahlen\Exception\ApiException;
use Barzahlen\Translate;

class Validate
{
    public function checkSlipType()
    {
        //Only payment, payout, refund
    }


    public function checkCustomerKey()
    {
        //check length and only allowed characters, numbers and special chars
    }

    public function checkTransaction($fAmount, $sIso3Currency)
    {

    }

    public function checkHookUrl()
    {
        //check if is https://
        //check if valid URL
    }

    public function checkExpiresAt()
    {
        //check if date format is correct 'Y-m-d\TH:i:s\Z'
        //check if is in future
    }

    public function checkCustomer(array $aCustomerData)
    {

    }

    /**
     * checks if language string is correct and language file exists
     * @param $sLang
     * @param bool $throwException
     * @return bool
     * @throws ApiException
     */
    public function checkLanguage($sLang, $throwException = false)
    {
        if(mb_strlen($sLang) != 5) {
            if($throwException)
                throw new ApiException('%s. Not a valid language like de_DE or en_GB. Check string length.', 'N/A', array($sLang), true);
            return false;
        }

        if($sLang{2} == '_') {
            if($throwException)
                throw new ApiException('%s. Not a valid language like de_DE or en_GB. Missing _.', 'N/A', array($sLang), true);
            return false;
        }

        if(!file_exists(Translate::LANGUAGE_FOLDER . DIRECTORY_SEPARATOR . $sLang . '.csv')) {
            if($throwException)
                throw new ApiException('%s.Not a valid language like de_DE or en_GB. Missing language file.', 'N/A', array($sLang), true);
            return false;
        }

        return true;
    }

}