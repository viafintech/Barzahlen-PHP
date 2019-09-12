<?php

namespace Barzahlen\Request;

class Autocorrect
{
    public function correctTransaction($fAmount, $sIso3Currency)
    {

    }

    public function correctHookUrl()
    {
        //correct if is not https://
        //correct if valid URL is false
    }

    public function correctExpiresAt()
    {
        //correct if date format is not correct 'Y-m-d\TH:i:s\Z'
    }

    public function correctCustomer(array $aCustomerData)
    {
        //correct language
        //correct phone
    }

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