<?php

namespace Barzahlen\Tests;

use Barzahlen\Request\Sanitize;
use Barzahlen\Request\Validate;

class SanitizeTest extends \PHPUnit\Framework\TestCase
{
    private $_oObject;
    
    public function setUp()
    {
        $this->_oObject = new Sanitize();
    }

    public function testCheckSlipType()
    {
        $this->assertEquals('payment', $this->_oObject->sanitizeSlipType('payment'));
        $this->assertEquals('payout', $this->_oObject->sanitizeSlipType('payout'));
        $this->assertEquals('refund', $this->_oObject->sanitizeSlipType('refund'));
    }

    public function testValidateCustomerKey()
    {
        $this->assertEquals('fdfdfdsfsdfsd#fds', $this->_oObject->sanitizeCustomerKey("fdfdfdsfsdfsd#fds"));
        $this->assertEquals(":;<=>LALA%&'342343434()*+,-.$+~fdfdfdsfsdfsd#fds", $this->_oObject->sanitizeCustomerKey(":;<=>LALA%&'342343434()*+,-.$+~fdfdfdsfsdfsd#fds"));
        $this->assertEquals("ssddsdsfdfdfdsfsdfsd#fds#ää!§$%&/()=?`", $this->_oObject->sanitizeCustomerKey("ssdd sds fdfdfdsf  sdfsd#fds #ää!§$%&/()=?`"));
    }

    public function testCheckTransaction()
    {
        $fAmount = '12.95';
        $sIso3Currency = 'EUR';

        $this->assertEquals(array('amount' => $fAmount, 'currency' => $sIso3Currency), $this->_oObject->sanitizeTransaction($fAmount, $sIso3Currency));

        $fAmount = '12.9555';
        $sIso3Currency = '€';

        $this->assertNotEquals(array('amount' => $fAmount, 'EUR' => $sIso3Currency), $this->_oObject->sanitizeTransaction($fAmount, $sIso3Currency));
    }

    public function testCheckHookUrl() {
        $this->assertEquals($this->_oObject->sanitizeHookUrl('https://www.example.com'));
        $this->assertEquals($this->_oObject->sanitizeHookUrl('https://aaa:bbb@www.example.com/fdfdf/fdf/?x=234&fgf=33d'));
    }

    public function sanitizeExpiresAt()
    {
        $oDate = new \DateTime();

        $sDate = $oDate->format('Y-d-s H:i:s');
        $sDateCorrect = $oDate->format('Y-m-d\TH:i:s\Z');
        $this->assertEquals($sDateCorrect , $this->_oObject->sanitizeExpiresAt($sDate));

    }
}
