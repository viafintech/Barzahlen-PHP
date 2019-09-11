<?php

namespace Barzahlen\Request;

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

	public function checkLanguage($sLang)
	{

	}

}