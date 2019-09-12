<?php
namespace Barzahlen;

class Session
{
    const SESSION_NAME = "BARZAHLEN_SDK";

    private static $bSessionState = false;

    public static function isSessionStarted()
    {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            self::$bSessionState = false;
            return false;
        } else {
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
    public function set($sName , $mValue )
    {
        $_SESSION[self::SESSION_NAME][$sName] = $mValue;
    }


    /**
     * get a session variable
     *
     * @param string $sName
     * @return mixed
     */
    public function get($sName)
    {
        return $_SESSION[self::SESSION_NAME][$sName];
    }


    /**
     * destroy session instance
     * @return bool
     */
    public function destroy()
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

Session::startSession();