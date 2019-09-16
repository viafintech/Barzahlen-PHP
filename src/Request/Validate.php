<?php
namespace Barzahlen\Request;

use Barzahlen\Exception\ApiException;
use Barzahlen\Translate;

class Validate
{
    /**
     * checks slip type
     *
     * @param $sSlipType
     * @return bool
     */
    public function checkSlipType($sSlipType)
    {
        //Only payment, payout, refund
        $aSlipTypes = array('payment', 'payout', 'refund');

        if(in_array($sSlipType, $aSlipTypes)) {
            return true;
        }

        return false;
    }


    /**
     * checks if customer key is in correct format
     * @param $sKey
     * @return bool
     */
    public function checkCustomerKey($sKey)
    {
        //Check length and only allowed characters, numbers and special chars
        if(mb_strlen($sKey) > 80) {
            return false;
        }

        if(!preg_match('^[a-zA-Z0-9!"#$%&\'()*+,-./:;<=>?@\[\\\]\^_\{\|\}~]+$', $sKey))
        {
            return false;
        }

        return true;
    }

    /**
     * @param $fAmount
     * @param $sIso3Currency
     * @param bool $bThrowException
     * @return bool
     * @throws ApiException
     */
    public function checkTransaction($fAmount, $sIso3Currency, $bThrowException = false)
    {
        //amount
        if(!preg_match('/^[0-9]+\.[0-9]{2}$/', $fAmount)) {
            if($bThrowException)
                throw new ApiException('%s. Not a valid float format for the amount like 13.23', 'N/A', array($fAmount), true);
            return false;
        }

        if(!preg_match('/^[a-zA-Z]{3}$/', $sIso3Currency)) {
            if($bThrowException)
                throw new ApiException('%s. Not a valid float format for the currency setting like or EUR, USD', 'N/A', array($fAmount), true);
            return false;
        }

        return true;
    }

    /**
     * @param $sUrl
     * @param bool $bThrowException
     * @return bool
     * @throws ApiException
     */
    public function checkHookUrl($sUrl, $bThrowException = false)
    {
        //check if is https://
        //check if valid URL
        if(!preg_match('%^(?:(?:https)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu', $sUrl)) {
            if($bThrowException)
                throw new ApiException('%s. Not a valid url format. A secure url (https) is essential.', 'N/A', array($sUrl), true);
            return false;
        }

        return true;
    }

    public function checkExpiresAt($sDate)
    {
        //check if date format is correct 'Y-m-d\TH:i:s\Z'
        //check if is in future
        try {
            $oDate = new \DateTime($sDate);
            if($oDate->format('Y-m-d\TH:i:s\Z') != $sDate) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }


    /**
     * checks customer data
     * @param array $aCustomerData
     * @param bool $bThrowException
     * @return bool
     * @throws ApiException
     */
    public function checkCustomer(array $aCustomerData, $bThrowException = false)
    {
        $aCustomerData['key'];

        if(!preg_match("/^(\+[0-9]{2,3}|0+[0-9]{2,5}).+[\d\s\/()-]/", $aCustomerData['cell_phone'])) {
            if($bThrowException)
                throw new ApiException('%s. Not a valid phone number like +492211234567. Check length and digit.', 'N/A', array($aCustomerData['cell_phone']), false);
            return false;
        }

        if (!filter_var($aCustomerData['email'], FILTER_VALIDATE_EMAIL)) {
            if($bThrowException)
                throw new ApiException('%s. Not a valid email address like email@email.com.', 'N/A', array($aCustomerData['email']), false);
            return false;
        }

        if (!preg_match ("/[a-zA-Z]{2}/", $aCustomerData['country'])) {
            if($bThrowException)
                throw new ApiException('%s. Not a valid email iso2 country DE or GB.', 'N/A', array($aCustomerData['country']), false);
            return false;
        }

        return true;
    }

    /**
     * @param $aAddress
     * @param bool $bThrowException
     * @return bool
     * @throws ApiException
     */
    public function checkAddress($aAddress, $bThrowException = true)
    {
        if(mb_strlen($aAddress['zipcode']) < 4 &&  preg_match('/\d/', $aAddress['zipcode'])) {
            if($bThrowException)
                throw new ApiException('%s. Not a valid zipcode like 50679. Check length and digit.', 'N/A', array($aAddress['zipcode']), true);
            return false;
        }

        if(mb_strlen($aAddress['city']) < 3 && !is_numeric($aAddress['street_and_no'])) {
            if($bThrowException)
                throw new ApiException('%s. Not a valid city like Berlin. Check length.', 'N/A', array($aAddress['zipcode']), true);
            return false;
        }

        if(mb_strlen($aAddress['street_and_no']) < 5 && !is_numeric($aAddress['street_and_no'])) {
            if($bThrowException)
                throw new ApiException('%s. Not a valid street name and number like Wiener Platz 12.', 'N/A', array($aAddress['zipcode']), true);
            return false;
        }

        return true;
    }

    /**
     * checks if language string is correct and language file exists
     * @param $sLang
     * @param bool $bThrowException
     * @return bool
     * @throws ApiException
     */
    public function checkLanguage($sLang, $bThrowException = true)
    {
        if(mb_strlen($sLang) != 5) {
            if($bThrowException)
                throw new ApiException('%s. Not a valid language like de_DE or en_GB. Check string length.', 'N/A', array($sLang), true);
            return false;
        }

        if($sLang{2} != '_') {
            if($bThrowException)
                throw new ApiException('%s. Not a valid language like de_DE or en_GB. Missing _.', 'N/A', array($sLang), true);
            return false;
        }

        if(!file_exists(getcwd() . DIRECTORY_SEPARATOR . Translate::LANGUAGE_FOLDER . DIRECTORY_SEPARATOR . $sLang . '.csv')) {
            if($bThrowException)
                throw new ApiException('%s.Not a valid language like de_DE or en_GB. Missing language file.', 'N/A', array($sLang), true);
            return false;
        }

        return true;
    }

}