<?php


namespace Kamille\Utils\Morphic\Generator2;


use ArrayToString\ArrayToStringTool;
use Bat\CaseTool;
use Bat\FileSystemTool;
use Bat\StringTool;
use DebugLogger\DebugLogger;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoInfoTool;

class MorphicGenerator2 implements MorphicGenerator2Interface
{


    protected $debugMode;
    protected $db2Tables;
    private $recreateCacheFile;
    private $cacheFile;
    private $debugLogger;
    private $configuration;


    public function __construct()
    {
        $this->debugMode = false;
        $this->recreateCacheFile = false;
        $this->cacheFile = "/tmp/MorphicGenerator2/quickpdo-basicinfo-{db}.php";
        $this->debugLogger = DebugLogger::create();
        $this->configuration = [];
    }

    public static function create()
    {
        return new static();
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    public function generate()
    {
        if ($this->db2Tables) {

            $tablesBasicInfo = $this->getTablesBasicInfo($this->db2Tables);
            foreach ($tablesBasicInfo as $table => $tableInfo) {
                $this->generateByTableInfo($tableInfo);
            }
        } else {
            // don't know this generation technique yet
        }
    }

    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    public function setTables($tables, $db = null)
    {
        if (null === $db) {
            $db = QuickPdoInfoTool::getDatabase();
        }
        $this->db2Tables[$db] = $tables;
        return $this;
    }

    public function debug($bool)
    {
        $this->debugMode = $bool;
        return $this;
    }


    public function recreateCache($bool)
    {
        $this->recreateCacheFile = $bool;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function generateByTableInfo(array $tableInfo)
    {
        $tableAdvancedInfo = $this->getAdvancedInfo($tableInfo);


//        $this->generateController($tableAdvancedInfo);
        $this->generateListConfigFile($tableAdvancedInfo);
//        $this->generateFormConfigFile($tableAdvancedInfo);
    }


    protected function getTablesBasicInfo(array $db2Tables)
    {
        $tablesBasicInfo = [];
        foreach ($db2Tables as $db => $tables) {
            $db2Tables = $this->getAllDbBasicInfo($db);
            foreach ($tables as $table) {
                $tablesBasicInfo[$table] = $db2Tables[$table];
            }
        }
        return $tablesBasicInfo;
    }


    protected function getTableBasicInfo($table, $db)
    {
        $hasPrimaryKey = false;
        $nullables = QuickPdoInfoTool::getColumnNullabilities($table);
        if (false === $nullables) {
            $nullables = [];
        }


        $fks = QuickPdoInfoTool::getForeignKeysInfo($table, $db);
        $reversedFks = [];
        foreach ($fks as $fk => $info) {
            $id = $info[0] . "." . $info[1];

            $reversedFks[$id][] = [
                $fk,
                $info[0],
                $info[1],
                $info[2],
            ];
        }


        return [
            "db" => $db,
            "table" => $table,
            "fks" => $fks,
            "reversedFks" => $reversedFks,
            "rks" => QuickPdoInfoTool::getReferencedKeysInfo($table, $db),
            "ric" => QuickPdoInfoTool::getPrimaryKey($table, $db, true, $hasPrimaryKey),
            "hasPrimaryKey" => $hasPrimaryKey,
            "columnNullables" => $nullables,
            "columns" => QuickPdoInfoTool::getColumnNames($table),
            "columnTypes" => QuickPdoInfoTool::getColumnDataTypes($table),
            "columnTypesPrecision" => QuickPdoInfoTool::getColumnDataTypes($table, true),
            "ai" => QuickPdoInfoTool::getAutoIncrementedField($table),
        ];
    }


    protected function generateController(array $tableInfo)
    {
        $s = '';
        $s .= $this->_getControllerClassHeader($tableInfo);
        $s .= $this->_getControllerRenderMethod($tableInfo);
        $s .= $this->_getControllerRenderWithParentMethod($tableInfo);
        $s .= $this->_getControllerRenderWithNoParentMethod($tableInfo);

        $s .= <<<EEE

}

EEE;
//        $this->phpCode($s);

    }


    protected function generateListConfigFile(array $tableInfo)
    {


        $table2Aliases = $this->_getTable2Aliases($tableInfo);

        $s = '';
        $s .= $this->_getListConfigFileHeader($tableInfo);
        $s .= $this->_getListConfigFileQuery($tableInfo, $table2Aliases);
        $s .= $this->_getListConfigFileConfArray($tableInfo, $table2Aliases);


        $this->phpCode($s);

    }


    protected function _getTable2Aliases(array $tableInfo)
    {
        $tablesWithoutPrefix = $this->getConfiguration("tablesWithoutPrefix", []);

        // find db prefixes (to find aliases)
        $reversedKeys = $tableInfo['reversedFks'];
        $dbPrefixes = [];
        $allTables = [];
        foreach ($reversedKeys as $fullTable => $v) {
            $p = explode(".", $fullTable);
            $table = array_pop($p);
            $allTables[] = $table;
            if (!in_array($table, $tablesWithoutPrefix, true)) {
                $q = explode('_', $table);
                if (count($q) > 1) {
                    $prefix = array_shift($q) . "_";
                    $dbPrefixes[] = $prefix;
                }
            }
        }
        $dbPrefixes = array_unique($dbPrefixes);
        $table2Aliases = OrmToolsHelper::getAliases($allTables, $dbPrefixes, ['h']);
        return $table2Aliases;
    }

    protected function _getListConfigFileHeader(array $tableInfo)
    {
        $s = '<?php ' . PHP_EOL;
        $s .= 'use Kamille\Utils\Morphic\Helper\MorphicHelper;' . PHP_EOL;
        return $s;
    }


    protected function _getListConfigFileQuery(array $tableInfo, array $table2Aliases)
    {
        // find db prefixes (to find aliases)
        $reversedKeys = $tableInfo['reversedFks'];

        $joins = [];
        foreach ($reversedKeys as $ftable => $colsInfo) {
            $p = explode('.', $ftable);
            $table = array_pop($p);
            $prefix = $table2Aliases[$table];

            $onClause = [];
            foreach ($colsInfo as $info) {
                $onClause[] = "$prefix." . $info[3] . "=h." . $info[0];
            }

//            $joins[] = "inner join $ftable $prefix on " . implode(' and ', $onClause);
            $joins[] = "inner join $table $prefix on " . implode(' and ', $onClause);
        }


        $sJoins = implode(PHP_EOL, $joins);


        $s = <<<EEE
        
\$q = "select %s from `$tableInfo[table]` h
$sJoins  
";
EEE;
        return $s;

    }


    protected function getNameByTable($table)
    {
        $tablesWithoutPrefix = $this->getConfiguration("tablesWithoutPrefix", []);

        /**
         * If a table contains an underscore, we assume that it is prefixed, unless
         * it is registered in the tablesWithoutPrefix array.
         */
        if (
            false !== strpos($table, "_") &&
            false === in_array($table, $tablesWithoutPrefix, true)
        ) {
            $p = explode("_", $table);
            array_shift($p); // drop the prefix
            $table = implode('_', $p);
        }
        $name = strtolower($table);
        $name = str_replace("_has_", '-', $name);
        return $name;
    }

    protected function _getListConfigFileConfArray(array $tableInfo, array $table2Aliases)
    {

        $viewId = $tableInfo["table"];
        $cols = $tableInfo["columns"];
        $fks = $tableInfo["fks"];
        $ric = $tableInfo["ric"];
        $rcMap = [];
        $headers = [];

        foreach ($cols as $col) {
            $label = $this->identifierToLabel($col);
            $headers[$col] = $label;
        }


        $reversedKeys = $tableInfo['reversedFks'];
        foreach ($reversedKeys as $fullTable => $v) {
            $p = explode(".", $fullTable);
            $db = $p[0];
            $table = $p[1];

            $prefix = $table2Aliases[$table];

            $ric = QuickPdoInfoTool::getAutoIncrementedField($table, $db);
            $repr = OrmToolsHelper::getRepresentativeColumn($fullTable);


            $name = $this->getNameByTable($table);
            $label = $this->identifierToLabel($name);
            $headers[$name] = $label;
            $rcMap[$name] = [];
            a($fullTable, $ric);
            foreach ($ric as $col) {
                $rcMap[$name][] = $prefix . "." . $col;
            }
            $rcMap[$name][] = $prefix . "." . $repr;


        }
        $headers['action'] = '';


        $headersVis = [];
        foreach ($fks as $col => $info) {
            $headersVis[$col] = false;
        }


        az($rcMap);
        $sHeaders = ArrayToStringTool::toPhpArray($headers, null, 4);
        $sHeadersVis = ArrayToStringTool::toPhpArray($headersVis, null, 4);
        $sRcMap = ArrayToStringTool::toPhpArray($rcMap, null, 4);


        $sRic = ArrayToStringTool::toPhpArray($ric, null, 4);

        $s = <<<EEE



\$parentValues = MorphicHelper::getListParentValues(\$q, \$context);



\$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "$tableInfo[labelPlural]",
    'table' => '$tableInfo[table]',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => '$viewId',
    "headers" => $sHeaders,
    "headersVisibility" => $sHeadersVis,
    "realColumnMap" => [
        'provider' => [
            'p.name',
            'p.id',
        ],
    ],
    'querySkeleton' => \$q,
    "queryCols" => [
        'h.shop_id',
        'h.product_id',
        'h.provider_id',
        'h.wholesale_price',
        'concat(p.id, ". ", p.name) as provider',
        'concat(s._discount_badge) as shop_has_product', // fix this
        'concat(s._discount_badge) as shop_has_product',
    ],
    "ric" => $sRic,
    "formRouteExtraVars" => \$parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProductHasProvider_List",    
    'context' => \$context,
];



EEE;

        return $s;
    }


    protected function guessLabelsByTable($table)
    {
        $prettyName = $this->getNameByTable($table);


        $label = str_replace("_", ' ', $prettyName);
        $labelPlural = StringTool::getPlural($label);

        return [
            $label,
            $labelPlural,
        ];
    }

    protected function getConfiguration($key, $default = null)
    {
        if (array_key_exists($key, $this->configuration)) {
            return $this->configuration[$key];
        }
        return $default;
    }

    protected function getTableRouteByTable($table)
    {
        $camel = $this->getCamelByTable($table);
        return "Morphic_Generated_" . $camel . "_List";
    }

    protected function getCamelByTable($table)
    {
        return CaseTool::snakeToFlexiblePascal($table);
    }


    protected function identifierToLabel($identifier)
    {
        return ucfirst(str_replace('_', ' ', $identifier));
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function d($msg, $type = 'info')
    {
        if (true === $this->debugMode) {
            $this->debugLogger->log($msg, $type, true);
        }
    }

    private function line($string, $extraIndent = 0, $br = false)
    {
        $s = '';
        $s .= str_repeat("\t", $extraIndent);
        $s .= $string;
        if ($br) {
            $s .= PHP_EOL;
        }
        return $s;
    }

    private function getAdvancedInfo(array $tableBasicInfo)
    {
        $a = $tableBasicInfo;
        list($label, $labelPlural) = $this->guessLabelsByTable($tableBasicInfo['table']);
        $a['label'] = $label;
        $a['labelPlural'] = $labelPlural;
        $a['camel'] = $this->getCamelByTable($tableBasicInfo['table']);
        $a['route'] = $this->getTableRouteByTable($a['table']);
        return $a;
    }

    private function getContentFromCache($db)
    {
        $f = $this->getCacheFile($db);
        if (file_exists($f)) {
            return unserialize(file_get_contents($f));
        }
        return false;
    }

    private function getCacheFile($db)
    {
        return str_replace('{db}', $db, $this->cacheFile);
    }


    private function getAllDbBasicInfo($db)
    {

        $f = $this->getCacheFile($db);
        if (true === $this->recreateCacheFile) {
            if (strlen($f) > 10) {
                FileSystemTool::remove($f);
            }
        }

        $ret = $this->getContentFromCache($db);
        if (false !== $ret) {
            return $ret;
        }

        $tables = QuickPdoInfoTool::getTables($db);
        foreach ($tables as $table) {
            $tablesBasicInfo[$table] = $this->getTableBasicInfo($table, $db);
        }

        FileSystemTool::mkfile($f, serialize($tablesBasicInfo));
        return $tablesBasicInfo;
    }


    private function phpCode($str)
    {
        echo '<hr>';
        echo '<pre>';
        echo $str;
        echo '</pre>';
        echo '<hr>';


        FileSystemTool::mkfile("/tmp/MorphicGenerator2/tmp.php", $str);
    }

    private function getRenderWithParentCodeBlock($fullTable, array $cols)
    {

        $p = explode(".", $fullTable);
        if (count($p) > 1) {
            $table = array_pop($p);
        } else {
            $table = array_shift($p);
        }

        list($label, $labelPlural) = $this->guessLabelsByTable($table);
        $route = $this->getTableRouteByTable($table);


        $aArrLinesGet = [];
        $aArrLines = [];
        foreach ($cols as $col) {
            $aArrLinesGet[] = '"' . $col . '" => $_GET["' . $col . '"],';
            $aArrLines[] = '"' . $col . '" => "' . $col . '",';
        }
        $sArrLinesGet = implode(PHP_EOL . "\t\t\t\t", $aArrLinesGet);
        $sArrLines = implode(PHP_EOL . "\t\t\t\t", $aArrLines);

        return <<<EEE
        
            return \$this->renderWithParent("$table", [
                $sArrLinesGet
            ], [
                $sArrLines
            ], [
                "$label",
                "$labelPlural",
            ], "$route");
EEE;
    }


    protected function _getControllerClassHeader(array $tableInfo)
    {


        $s = <<<EEE
<?php

namespace Controller\Morphic\Generated\\$tableInfo[camel];
use Controller\Morphic\Pattern\MorphicListController;
use Kamille\Utils\Morphic\Exception\MorphicException;


class $tableInfo[camel]ListController extends MorphicListController
{

EEE;

        return $s;

    }


    protected function _getControllerRenderMethod(array $tableInfo)
    {
        $table = $tableInfo['table'];
        $reversedFks = $tableInfo['reversedFks'];
        $this->d('generating controller for table ' . $table);


        //--------------------------------------------
        // RENDER
        //--------------------------------------------
        $s = <<<EEE
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
EEE;

        $mul = PHP_EOL . "\t\t\t";


        //--------------------------------------------
        // PARENTS FROM URI?
        //--------------------------------------------
        $c = 0;
        foreach ($reversedFks as $fkFullTable => $colsInfo) {
            $conds = [];
            $s .= PHP_EOL . "\t\t";

            if (0 === $c++) {
                $s .= 'if ( ';
            } else {
                $s .= 'elseif ( ';
            }

            foreach ($colsInfo as $info) {
                $conds[] = 'array_key_exists ( "' . $info[0] . '", $_GET)';
            }

            if (count($conds) > 1) {
                $s .= $mul;
            }

            $s .= implode(' &&' . $mul, $conds);

            if (count($conds) > 1) {
                $s .= PHP_EOL . "\t\t";
            }
            $s .= ') {';

            $cols = [];
            foreach ($colsInfo as $info) {
                $cols[] = $info[0];
            }
            $s .= $this->getRenderWithParentCodeBlock($fkFullTable, $cols);
        }

        $s .= PHP_EOL . "\t\t" . '}';

        $s .= PHP_EOL . "\t\t";
        $s .= 'return $this->renderWithNoParent();';
        $s .= <<<EEE
        
    }
    
EEE;
        return $s;
    }


    protected function _getControllerRenderWithParentMethod(array $tableInfo)
    {
        $ric = $tableInfo['ric'];
        $ricCols = [];
        foreach ($ric as $col) {
            $ricCols[] = '"' . $col . '",';
        }
        $sRic = implode(PHP_EOL . "\t\t\t\t", $ricCols);

        $s = <<<EEE

    protected function renderWithParent(\$parentTable, array \$parentKey2Values, array \$parentKeys2ReferenceKeys, array \$labels, \$route)
    {
        \$elementInfo = [
            'table' => "$tableInfo[table]",
            'ric' => [
                $sRic
            ],
            'label' => "$tableInfo[label]",
            'labelPlural' => "$tableInfo[labelPlural]",
            'route' => "$tableInfo[route]",
        ];
        return \$this->doRenderWithParent(\$elementInfo, \$parentTable, \$parentKey2Values, \$parentKeys2ReferenceKeys, \$labels, \$route);
    }
    
EEE;
        return $s;
    }

    protected function _getControllerRenderWithNoParentMethod(array $tableInfo)
    {
        $ric = $tableInfo['ric'];
        $ricCols = [];
        foreach ($ric as $col) {
            $ricCols[] = '"' . $col . '",';
        }
        $sRic = implode(PHP_EOL . "\t\t\t\t\t", $ricCols);

        $sExtra = $this->_getControllerRenderWithNoParentMethodExtraVar($tableInfo);


        $s = <<<EEE
        
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return \$this->doRenderFormList([
                'title' => "$tableInfo[labelPlural]",
                'breadcrumb' => "$tableInfo[table]",
                'form' => "$tableInfo[table]",
                'list' => "$tableInfo[table]",
                'ric' => [
                    $sRic
                ],

                "newItemBtnText" => "Add a new $tableInfo[label]",
                "newItemBtnLink" => E::link("$tableInfo[route]") . "?form",
                $sExtra
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
EEE;
        return $s;
    }

    protected function _getControllerRenderWithNoParentMethodExtraVar(array $tableInfo)
    {
        return '';
    }
}




