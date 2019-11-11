<?php

namespace Barzahlen\Tests;

use Barzahlen\Request\Validate;
use Barzahlen\Request\Autocorrect;

class AutocorrectTest extends \PHPUnit\Framework\TestCase
{
    private $_oObject;

    public function setUp()
    {
        $this->_oObject = new Validate();
    }

    public function testCorrectTransaction()
    {
        //correct values need to stay the same
        $fAmount = '12.95';
        $sIso3Currency = 'EUR';

        //Test correct values
        $aCorrected = $this->_oObject->correctTransaction($fAmount, $sIso3Currency);

        $this->assertEquals($fAmount, $aCorrected['amount']);
        $this->assertEquals($sIso3Currency, $aCorrected['currency']);

        //correct values need to stay the same
        $fAmountWrong = '12.9511';
        $sIso3CurrencyWrong = '€';

        //Test incorrect values
        $aCorrected = $this->_oObject->correctTransaction($fAmountWrong, $sIso3CurrencyWrong);

        //Test against correct values, values should have been corrected automatically
        $this->assertEquals($fAmount, $aCorrected['amount']);
        $this->assertEquals($sIso3Currency, $aCorrected['currency']);
    }

    public function testCorrectHookUrl() {

        //check against correct value
        $sUrl = "https://api.barzahlen.de";
        $this->assertEquals($sUrl, $this->_oObject->correctHookUrl($sUrl));

        //check against incorrect value, which should have been corrected
        $sUrlWrong = "http://api.barzahlen.de"; //missing https
        $this->assertEquals($sUrl, $this->_oObject->correctHookUrl($sUrlWrong));
    }

    
}