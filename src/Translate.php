<?php
namespace Barzahlen;

class Translate
{

	protected static $sLanguage = "de_DE";

	public static function setLanguage($sLang)
	{
		self::$sLanguage = $sLang;
	}

	public static function getLanguage()
	{
		return self::$sLanguage;
	}

	/**
	 * translates the current string
	 * @param string $sString
	 * @param array $aParams
	 */
	public static function __T($sString, array $aParams)
	{

	}
}
