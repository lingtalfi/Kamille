<?php


namespace Kamille\Utils\Session;


use Bat\SessionTool;

class KamilleSession
{


    public static function set($k, $v)
    {
        SessionTool::start();
        if (false === array_key_exists(self::getSessionName(), $_SESSION)) {
            $_SESSION[self::getSessionName()] = [];
        }
        $_SESSION[self::getSessionName()][$k] = $v;
    }

    public static function get($k, $default = null)
    {
        SessionTool::start();
        if (array_key_exists(self::getSessionName(), $_SESSION)) {
            if (array_key_exists($k, $_SESSION[self::getSessionName()])) {
                return $_SESSION[self::getSessionName()][$k];
            }
        }
        return $default;
    }

    public static function all()
    {
        SessionTool::start();
        if (array_key_exists(self::getSessionName(), $_SESSION)) {
            return $_SESSION[self::getSessionName()];
        }
        return [];
    }


    public static function has($k)
    {
        SessionTool::start();
        if (array_key_exists(self::getSessionName(), $_SESSION)) {
            return array_key_exists($k, $_SESSION[self::getSessionName()]);
        }
        return false;
    }

    public static function remove($k)
    {
        SessionTool::start();
        if (array_key_exists(self::getSessionName(), $_SESSION)) {
            unset($_SESSION[self::getSessionName()][$k]);
        }
        return false;
    }


    public static function pick($k, $default = null)
    {
        SessionTool::start();
        if (array_key_exists(self::getSessionName(), $_SESSION)) {
            if (array_key_exists($k, $_SESSION[self::getSessionName()])) {
                $value = $_SESSION[self::getSessionName()];
                unset($_SESSION[self::getSessionName()][$k]);
                return $value;
            }
        }
        return $default;
    }


    //--------------------------------------------
    // 
    //--------------------------------------------
    protected static function getSessionName()
    {
        return "kamille";
    }
}