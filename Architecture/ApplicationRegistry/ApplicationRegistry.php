<?php


namespace Kamille\Architecture\ApplicationRegistry;


use Kamille\Architecture\ApplicationRegistry\Exception\ApplicationRegistryException;


/**
 *
 * This object is created so that widgets and modules can communicate with each others in
 * any direction (widget <-> widget, widget <-> module, module <-> module).
 *
 * In fact, any code can use the ApplicationRegistry to transmit information to an end.
 *
 *
 */
class ApplicationRegistry
{
    private static $vars = [];

    public static function setVar($key, $value)
    {
        self::$vars[$key] = $value;
    }

    public static function getVar($key, $default = null, $throwEx = false)
    {
        if (array_key_exists($key, self::$vars)) {
            return self::$vars[$key];
        }
        if (true === $throwEx) {
            throw new ApplicationRegistryException("Key not found in the registry: $key");
        }
        return $default;
    }


}