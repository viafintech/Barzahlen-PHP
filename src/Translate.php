<?php
namespace Barzahlen;

use Barzahlen\Request\Autocorrect;
use Barzahlen\Request\Validate;
use Barzahlen\Exception\ApiException;

class Translate
{
    protected static $sLanguage = "en_GB";
    private static $_oValidate;
    private static $_oAutocorrect;
    private static $bInitialized = false;

    public static function init() {
        self::autodetectLanguage();
        self::$bInitialized = true;
    }

    /**
     * @throws ApiException
     */
    public static function autodetectLanguage()
    {
        $sLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        self::setLanguage($sLang);
    }

    /**
     * @param $sLang
     * @throws ApiException
     */
    public static function setLanguage($sLang)
    {
        self::$_oValidate = new Validate();
        self::$_oAutocorrect = new Autocorrect();

        try
        {

            $bLanguage = self::$_oValidate->checkLanguage($sLang);

            if(!$bLanguage) {
                $sLang = self::$_oAutocorrect->correctLanguage($sLang);
            }

            $bLanguage = self::$_oValidate->checkLanguage($sLang);

            if(!$bLanguage) {
                throw new ApiException( 'No valid language string given.', 'N/A', );
            }

        } catch (ApiException $e) {

        }

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
