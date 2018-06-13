<?php


namespace Kamille\Utils\Morphic\Util\ModuleConfiguration;


use Kamille\Utils\Morphic\Exception\MorphicModuleConfigurationException;
use QuickPdo\QuickPdo;

class MorphicModuleConfigurationTool
{

    private static $table = null;


    public static function get(string $key, $default = null, $throwEx = false, string $table = null)
    {
        if (null === $table) {
            $table = static::$table;
        }

        if ($table) {

            $value = QuickPdo::fetch("select the_value from $table where the_key=:thekey", [
                "thekey" => $key,
            ], \PDO::FETCH_COLUMN);
            if (false !== $value) {
                return $value;
            }
            if (false === $throwEx) {
                return $default;
            }
            throw new MorphicModuleConfigurationException("Value not found in table $table with key $key");
        } else {
            throw new MorphicModuleConfigurationException("table not defined");
        }
    }
}