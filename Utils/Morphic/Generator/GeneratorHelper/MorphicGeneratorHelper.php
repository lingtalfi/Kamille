<?php


namespace Kamille\Utils\Morphic\Generator\GeneratorHelper;


use ArrayToString\ArrayToStringTool;
use Bat\StringTool;
use PhpFile\PhpFile;
use QuickPdo\QuickPdoInfoTool;

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


    public static function getEnglishDictionaryCode(array $prefixes = [], $skipIfNoPrefixMatch = true)
    {


        //--------------------------------------------
        // COLLECT OBJECT TABLES
        // CREATE THE DICTIONARY ENTRIES
        //--------------------------------------------
        $dic = [];
        $objectTables = [];
        $db = QuickPdoInfoTool::getDatabase();
        $tables = QuickPdoInfoTool::getTables($db);
        sort($tables);


        $prefix2Lengths = [];
        foreach ($prefixes as $prefix) {
            $prefix2Lengths[$prefix] = strlen($prefix);
        }

        foreach ($tables as $table) {
            if (false === strpos($table, '_has_')) {
                $objectTables[] = $table;
                $label = $table;

                $match = false;
                foreach ($prefix2Lengths as $prefix => $length) {
                    if (0 === strpos($table, $prefix)) {
                        $label = substr($label, $length);
                        $match = true;
                        break;
                    }
                }

                if (false === $match && true === $skipIfNoPrefixMatch) {
                    continue;
                }

                $label = str_replace('_', ' ', $label);

                $labelPlural = StringTool::getPlural($label);

                $dic[$table] = [
                    0 => $label,
                    1 => $labelPlural,
                ];

            }
        }


        //--------------------------------------------
        // COLLECT THE DICTIONARY ENTRY
        //--------------------------------------------
        $s = '$dictionary = ' . ArrayToStringTool::toPhpArray($dic);
        return $s;

    }


    /**
     * @param $hasTable
     * @return array|false
     */
    public static function getContextFieldsByHasTable($hasTable, $prefixes = null)
    {
        $ret = [];
        $originalTable = $hasTable;
        if (null !== $prefixes) {
            if (!is_array($prefixes)) {
                $prefixes = [$prefixes];
            }
            foreach ($prefixes as $prefix) {
                if (0 === strpos($hasTable, $prefix)) {
                    $hasTable = substr($hasTable, strlen($prefix));
                    break;
                }
            }
        }

        $p = explode('_has_', $hasTable);
        if (count($p) > 1) {
            $fkeys = QuickPdoInfoTool::getForeignKeysInfo($originalTable);
            array_pop($p); // drop the right part
            foreach ($p as $cue) {
                $col = $cue . "_id";
                if (array_key_exists($col, $fkeys)) {
                    $ret[] = $col;
                }
            }
            return $ret;
        }
        return false;
    }


}