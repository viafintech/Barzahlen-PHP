<?php

namespace Barzahlen\Tests;

use Barzahlen\Request\Validate;

class ValidationTest extends \PHPUnit\Framework\TestCase
{
    private $_oObject;
    
    public function setUp()
    {
        $this->_oObject = new Validate();
    }

    public function testCheckSlipType()
    {
        $this->assertTrue($this->_oObject->checkSlipType('payment'));
        $this->assertTrue($this->_oObject->checkSlipType('payout'));
        $this->assertTrue($this->_oObject->checkSlipType('refund'));
        $this->assertFalse($this->_oObject->checkSlipType('undefined'));
    }

    public function testValidateCustomerKey()
    {
        $this->assertTrue($this->_oObject->checkCustomerKey("fdfdfdsfsdfsd#fds"));
        $this->assertTrue($this->_oObject->checkCustomerKey(":;<=>LALA%&'342343434()*+,-.$+~fdfdfdsfsdfsd#fds"));
        $this->assertFalse($this->_oObject->checkCustomerKey("ssdd sds fdfdfdsf  sdfsd#fds #ää!§$%&/()=?`"));
        $this->assertFalse($this->_oObject->checkCustomerKey("dzrl2ja40azjf9ftm4a9gkq4hhs8ba4212mm5yzrour9gbe1asr4k01ab6rdzsoiiw44t9dclduu9iio52kx8"));
    }

    public function testCheckTransaction()
    {
        $fAmount = '12.95';
        $sIso3Currency = 'EUR';

        $this->assertTrue($this->_oObject->checkTransaction($fAmount, $sIso3Currency, true));

        $fAmount = '12.9555';
        $sIso3Currency = '€';

        $this->assertFalse($this->_oObject->checkTransaction($fAmount, $sIso3Currency));

        $fAmount = '12.9555';
        $sIso3Currency = 'EUR';

        $this->assertFalse($this->_oObject->checkTransaction($fAmount, $sIso3Currency));

        $fAmount = '12.95';
        $sIso3Currency = '$';

        $this->assertFalse($this->_oObject->checkTransaction($fAmount, $sIso3Currency));

        $fAmount = '1001.00';
        $sIso3Currency = 'EUR';

        $this->assertFalse($this->_oObject->checkTransaction($fAmount, $sIso3Currency));
    }

    public function testCheckHookUrl() {
        $this->assertTrue($this->_oObject->checkHookUrl('https://www.example.com'));
        $this->assertTrue($this->_oObject->checkHookUrl('https://aaa:bbb@www.example.com/fdfdf/fdf/?x=234&fgf=33d'));
        $this->assertFalse($this->_oObject->checkHookUrl('http://aaa:bbb@www.example.com/fdfdf/fdf/?x=234&fgf=33d'));
        $this->assertFalse($this->_oObject->checkHookUrl('https:// shouldfail.com'));
    }

    public function checkExpiresAt()
    {
        $oDate = new \DateTime();

        $sDate = $oDate->format('Y-d-s H:i:s');
        $this->assetFalse($this->_oObject->checkExpiresAt($sDate));

    }
}
