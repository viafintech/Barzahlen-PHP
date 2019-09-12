<?php

namespace Barzahlen\Request;

class Sanitize
{
    public function sanitizeSlipType()
    {
    }


    public function sanitizeCustomerKey()
    {
        //sanitize length and only allowed characters, numbers and special chars
    }

    public function sanitizeTransaction($fAmount, $sIso3Currency)
    {

    }

    public function sanitizeHookUrl()
    {
        //sanitize if is https://
        //sanitize if valid URL
    }

    public function sanitizeExpiresAt()
    {
        //sanitize if date format is correct 'Y-m-d\TH:i:s\Z'
        //sanitize if is in future
    }

    public function sanitizeCustomer(array $aCustomerData)
    {

    }

}