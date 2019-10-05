<?php

namespace Barzahlen\Request;

use Barzahlen\Exception\ApiException;

class Autocorrect
{
    /**
     * correct amount and currency
     * 
     * @param $fAmount
     * @param $sIso3Currency
     * @return array
     */
    public function correctTransaction($fAmount, $sIso3Currency)
    {
        //check if currency string is in amount
        if(is_string($fAmount)) {
            $aReplace = array('€', 'EUR', '$', 'USD');
            $fAmount = str_replace($aReplace,'',$fAmount);
            $fAmount = trim($fAmount);
        }

        //check floats with comma as decimal separator
        if(mb_substr_count($fAmount,',') == 1) {
            $fAmount = str_replace(',','.',$fAmount);
        }

        //check floats with comma as decimal separator
        if(is_numeric($fAmount)) {
            $fAmount = floatval($fAmount);
            $fAmount = $this->roundFloat($fAmount);
        }

        $sIso3Currency = trim($sIso3Currency);

        switch($sIso3Currency) {
            case '€':
            case 'EU':
            case 'Euro':
                $sIso3Currency = 'EUR';
                break;
            case '$':
            case 'Dollar':
            case 'UsDollar':
                $sIso3Currency = 'USD';
                break;
        }

        return array('amount' => $fAmount, 'currency' => $sIso3Currency);
    }

    /**
     * round to 2 decimals
     *
     * @param $fAmount
     * @return float
     */
    public function roundFloat($fAmount)
    {
        $fAmount = round($fAmount, 2);
        return $fAmount;
    }

    /**
     * correct hook url
     * @param $sUrl
     * @return mixed
     */
    public function correctHookUrl($sUrl)
    {
        //escape whitespaces
        if(urldecode($sUrl) == $sUrl) {
            $aUrl = @parse_url($sUrl);
            if(!empty($aUrl)) {
                if(!empty($aUrl['path'])) {
                    $aUrl['path'] = urlencode($aUrl['path']);
                    $sUrl = $this->_unparseUrl($aUrl);
                }
            }
        }

        //correct if is not https://
        if(mb_substr_count($sUrl, 'https') == 0) {
            $sUrl = str_replace('http://', 'https://', $sUrl);
        }

        return $sUrl;
    }

    /**
     * returns a url from a parsed url array
     *
     * @param $aUrl
     * @return string
     */
    protected function _unparseUrl($aUrl) {
        $sScheme   = isset($aUrl['scheme']) ? $aUrl['scheme'] . '://' : '';
        $sHost     = isset($aUrl['host']) ? $aUrl['host'] : '';
        $sPort     = isset($aUrl['port']) ? ':' . $aUrl['port'] : '';
        $sUser     = isset($aUrl['user']) ? $aUrl['user'] : '';
        $sPass     = isset($aUrl['pass']) ? ':' . $aUrl['pass']  : '';
        $sPpass     = (!empty($sUser) || !empty($sPass)) ? $sPass . '@' : '';
        $sPath     = isset($aUrl['path']) ? $aUrl['path'] : '';
        $sQuery    = isset($aUrl['query']) ? '?' . $aUrl['query'] : '';
        $sFragment = isset($aUrl['fragment']) ? '#' . $aUrl['fragment'] : '';

        return $sScheme . $sUser . $sPass . $sHost . $sPort. $sPath . $sQuery . $sFragment;
    }

    /**
     * try to correct expires date
     *
     * @param string $sDate
     * @return string
     * @throws ApiException
     */
    public function correctExpiresAt($sDate)
    {
        //correct if date format is not correct 'Y-m-d\TH:i:s\Z'
        try {
            $oDate = new \DateTime(strtotime($sDate));
            return $oDate->format('Y-m-d\TH:i:s\Z');
        }  catch (\Exception $e) {
            $oE = new ApiException($e->getMessage(),'N/A', array(), true);
        }
    }

    /**
     * try to correct customer data
     *
     * @param array $aCustomerData
     */
    public function correctCustomer(array $aCustomerData)
    {
        //correct language
        $aCustomerData['language'] = $this->correctLanguage($aCustomerData['language']) ;

        //correct phone
        $aCustomerData['cell_phone'] = filter_var($aCustomerData['cell_phone'],FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * try to correct language
     *
     * @param $sLang
     * @return string
     */
    public function correctLanguage($sLang) {
        switch ($sLang)
        {
            case 'en':
            case 'english':
            case 'englisch':
                return 'en_GB';
            case 'de':
            case 'deu':
            case 'deutsch':
                return 'de_DE';
            default:
                return $sLang;
        }
    }

}