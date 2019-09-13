<?php
namespace Barzahlen;

class Session
{
    const SESSION_NAME = "BARZAHLEN_SDK";

    /**
     * is session started
     * @var bool
     */
    private static $bSessionState = false;

    /**
     * checks if session was started
     * @return bool
     */
    public static function isSessionStarted()
    {
        if(self::$bSessionState)
            return true;

        if(session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        } else {
            self::$bSessionState = true;
            return true;
        }
    }

    /**
     * starts session, if not started
     * @return bool
     */
    public static function startSession()
    {
        if (self::$bSessionState == false )
        {
            if(!self::isSessionStarted()) {
                self::$bSessionState = session_start();
            }
            else {
                self::$bSessionState = true;
            }
        }

        return self::$bSessionState;
    }

    /**
     * stores an entry in the session
     * @param string $sName
     * @param mixed $mValue
     */
    public static function set($sName , $mValue )
    {
        $_SESSION[self::SESSION_NAME][$sName] = $mValue;
    }


    /**
     * get a session variable
     *
     * @param string $sName
     * @return mixed
     */
    public static function get($sName)
    {
        return $_SESSION[self::SESSION_NAME][$sName];
    }


    /**
     * destroy session instance
     * @return bool
     */
    public static function destroy()
    {
        if (self::$bSessionState == true)
        {
            unset($_SESSION[self::SESSION_NAME]);
            if(empty($_SESSION)) {
                session_destroy();
            }
        }

        return self::$bSessionState = false;
    }
}

/**
 * start session automatically when loading this file
 */
Session::startSession();