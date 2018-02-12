<?php


namespace Kamille\Utils\Morphic\Generator\GeneratorHelper;


class MorphicGeneratorHelper
{

    public static function getColumnLabel($columnName, array $operation, array $config)
    {
        $viewId = $operation['elementName'];

        if (array_key_exists("columnLabels", $config)) {
            $labels = $config['columnLabels'];
            $table = array_key_exists("table", $labels) ? $labels['table'] : [];
            $default = array_key_exists("default", $labels) ? $labels['default'] : [];

            if (array_key_exists($viewId, $table) && array_key_exists($columnName, $table[$viewId])) {
                return $table[$viewId][$columnName];
            } elseif (array_key_exists($columnName, $default)) {
                return $default[$columnName];
            }
        }

        /**
         * If everything else fails, we use the default heuristic
         */
        $label = str_replace("_", " ", $columnName);
        return ucfirst($label);
    }
}