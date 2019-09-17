<?php

namespace Barzahlen\Request;

use Barzahlen\Exception\ApiException;

class Sanitize
{
    /**
     *
     *
     * @param $sSlipType
     * @return string|bool
     * @throws ApiException
     */
    public function sanitizeSlipType($sSlipType)
    {
        //Only payment, payout, refund
        $aSlipTypes = array('payment', 'payout', 'refund');

        if(in_array($sSlipType, $aSlipTypes)) {
            return $sSlipType;
        }

        throw new ApiException('Invalid slip type %s.','N/A',array($sSlipType));

    }


    /**
     * sainitizes customer key
     * @param $sKey
     * @return string
     */
    public function sanitizeCustomerKey($sKey)
    {
        return trim(preg_replace('/\s/', '', $sKey));
    }

    /**
     * sanitizes transaction values amount and currency
     * @param $fAmount
     * @param $sIso3Currency
     * @return array
     */
    public function sanitizeTransaction($fAmount, $sIso3Currency)
    {
        return array('amount' => floatval($fAmount), 'currency' => (string)$sIso3Currency);
    }

    /**
     * sanitize URL
     *
     * @param $sUrl
     * @return mixed|string
     */
    public function sanitizeHookUrl($sUrl)
    {
        //sanitize if valid URL, escape whitespaces
        $sUrl = trim(preg_replace('/\s/', '%20', $sUrl));

        //sanitize if is https://
        $sUrl = str_replace('http://', 'https://', $sUrl);
        return $sUrl;
    }

    public function sanitizeExpiresAt($sDate)
    {
        //sanitize if date format is correct 'Y-m-d\TH:i:s\Z'
        //sanitize if is in future
        $oDate = new \DateTime(strtotime($sDate));
        return $oDate->format('Y-m-d\TH:i:s\Z');
    }

    public function sanitizeCustomer($aCustomerData)
    {
        return $aCustomerData;

    }

    public function sanitizeAddress($aAddress)
    {
        return $aAddress;

    }

}