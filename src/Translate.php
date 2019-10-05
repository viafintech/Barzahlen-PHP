<?php
namespace Barzahlen;

use Barzahlen\Request\Autocorrect;
use Barzahlen\Request\Validate;

class Translate
{
    /**
     *
     */
    const LANGUAGE_FOLDER = 'Language';

    /**
     * language file to check last date
     */
    const LANG_CHECK = 'last_language_check.log';

    /**
     * currently set language
     * @var string
     */
    protected static $sLanguage = "en_GB";

    /**
     * Object of validation class
     * @var
     */
    private static $_oValidate;

    /**
     * Object of auto correct class
     * @var object
     */
    private static $_oAutoCorrect;

    /**
     * check if language settings are initialized
     * @var bool
     */
    private static $_bInitialized = false;

    /**
     *
     * @var array
     */
    protected static $_aTranslation;

    /**
     * if set to true a translation warning will be displayed for missing translations
     * @var bool
     */
    public static $bShowTranslationWarnings = false;

    /**
     * initialize translation and language system
     */
    public static function init() {
        if(!self::$_bInitialized) {
            self::autodetectLanguage();
        }

        self::$_bInitialized = true;
    }

    /**
     * automatically detects language via browser
     */
    public static function autodetectLanguage()
    {
        $sLang = self::$sLanguage;

        if(isset($_SERVER) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $sLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }

        self::downloadTranslation();

        if($sLang != self::$sLanguage)
            self::setLanguage($sLang);
    }

    /**
     * set or override language settings (use iso format e.g. de_DE, en_GB etc.)
     * @param $sLang
     * @param bool $bThrowErrors
     */
    public static function setLanguage($sLang, $bThrowErrors = false)
    {
        self::$_oValidate = new Validate();
        self::$_oAutoCorrect = new Autocorrect();

        try
        {
            $bLanguage = self::$_oValidate->checkLanguage($sLang);

            if(!$bLanguage) {
                $sLang = self::$_oAutoCorrect->correctLanguage($sLang);
            }

            $bLanguage = self::$_oValidate->checkLanguage($sLang, true);

            if(!$bLanguage && $bThrowErrors) {
                throw new \Exception( 'No valid language string given. Or file not found');
            }

            if($bLanguage)
                self::loadTranslation();

        } catch (\Exception $e) {
            trigger_error($e->getMessage());
        }

        self::$sLanguage = $sLang;
    }

    /**
     * current set language
     * @return string
     */
    public static function getLanguage()
    {
        return self::$sLanguage;
    }

    /**
     * translates the current string
     * @param string $sString
     * @param array $aParams
     * @return string
     */
    public static function __T($sString, $aParams = array())
    {
        $aText = $sString;

        try {
            if (!self::$_bInitialized) {
                self::init();
                throw new \Exception('language not initialized', 'N/A', array(), true);
            }

            if(empty(self::$_aTranslation)) {
                self::loadTranslation();
            }

            if (!empty(self::$_aTranslation) && array_key_exists($sString, self::$_aTranslation)) {
                $sString = self::$_aTranslation[$sString];
            } else {
                if (self::$bShowTranslationWarnings) {
                    $sLogMsg = 'No translation found for key "' . $sString . '"';
                    trigger_error($sLogMsg);
                }
            }

            if (!empty($aParams)) {
                $aText = self::applyParams($sString, $aParams);
            } else {
                $aText = $sString;
            }
        }
        catch (\Exception $e) {
            trigger_error($e->getMessage());
        }

        return $aText;
    }

    /**
     * loads translation
     * @param string $sLocale
     */
    protected static function loadTranslation($sLocale = '')
    {
        // Build locale string
        if(empty($sLocale))
            $sLocale = self::getLanguage();

        $sVar = '';
        if(basename(getcwd()) == "src" || basename(getcwd()) == "tests") {
            $sVar = ".." .DIRECTORY_SEPARATOR;
        }

        $sFileName = $sVar . 'src' . DIRECTORY_SEPARATOR . self::LANGUAGE_FOLDER . DIRECTORY_SEPARATOR . $sLocale . '.csv';
        self::$_aTranslation = self::readTranslationFile($sFileName);
    }

    /**
     * Retrieve the contents of the given file as translation hash
     *
     * @param string $sFileName
     * @return array
     */
    public static function readTranslationFile($sFileName)
    {
        $aTranslation = array();

        if (!file_exists($sFileName) || !is_readable($sFileName)) {
            trigger_error('no translation file found in ' . $sFileName);
            return $aTranslation;
        }

        $r = fopen($sFileName, 'r');
        while ($aRow = fgetcsv($r)) {
            if (isset($aRow[0])) {
                $aTranslation[$aRow[0]] = isset($aRow[1]) ? $aRow[1] : null;
            }
        }
        fclose($r);

        return $aTranslation;
    }

    /**
     * replaces placeholders '%s' by params
     *
     * @param $sString
     * @param $aParams
     * @return string
     */
    protected static function applyParams($sString, $aParams)
    {
        $sText = vsprintf($sString, $aParams);
        return $sText;
    }


    /**
     * downloads translation files if this has not been done in the last 24 hours
     */
    protected static function downloadTranslation()
    {
        try {
            if (file_exists(self::LANG_CHECK) && time()-filemtime(self::LANG_CHECK) > 24 * 3600) {
                //@todo download new translation files
                file_put_contents(self::LANG_CHECK, ' ');
            }
        } catch(\Exception $e) {
            trigger_error($e->getMessage());
        }

    }
}

Translate::init();