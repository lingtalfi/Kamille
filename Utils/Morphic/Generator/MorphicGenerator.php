<?php


namespace Kamille\Utils\Morphic\Generator;


use Bat\CaseTool;
use Bat\FileSystemTool;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Kamille\Utils\Morphic\Generator\ConfigFileGenerator\ConfigFileGeneratorInterface;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoInfoTool;

abstract class MorphicGenerator implements MorphicGeneratorInterface
{


    private $_file;
    private $conf;
    private $formConfigFileGen;


    public function __construct()
    {
        $this->conf = [];
    }

    public static function create()
    {
        return new static();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function generateByFile($file)
    {
        $this->_file = $file;
        $configuration = [];
        $operations = [];
        include $this->_file;
        $this->conf = $configuration;


        foreach ($operations as $operation) {
            $operation = $this->prepareOperation($operation);
            $this->executeOperation($operation);
        }
    }

    public function setFormConfigFileGen(ConfigFileGeneratorInterface $formConfigFileGen)
    {
        $this->formConfigFileGen = $formConfigFileGen;
        return $this;
    }






    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * Sanitizer function to flatten the operation structure.
     * The operation structure is defined in
     * the "Kamille/Utils/Morphic/Generator/morphic-generator-brainstorm-2.md" file.
     *
     * - ?operationType
     * - elementTable
     * - elementName
     * - elementLabel (singular)
     * - elementLabelPlural
     * - elementRoute
     * - ric
     * - ...more custom fields
     *
     *
     * This function will add the following:
     * - columns: array of columns contained in the table
     * - columnTypes: array of column names => column types (mysql types)
     * - CamelCase: the CamelCase version of the element name
     *
     *
     *
     *
     *
     */
    protected function prepareOperation(array $operation)
    {
        if (false === array_key_exists("operationType", $operation)) {
            $operation['operationType'] = "create";
        }
        $operation['columns'] = QuickPdoInfoTool::getColumnNames($operation['elementTable']);
        if (!is_array($operation['ric'])) {
            $operation['ric'] = [$operation['ric']];
        }
        $operation['columns'] = QuickPdoInfoTool::getColumnNames($operation['elementTable']);
        $operation['columnTypes'] = QuickPdoInfoTool::getColumnDataTypes($operation['elementTable']);
        $operation['columnFkeys'] = QuickPdoInfoTool::getForeignKeysInfo($operation['elementTable']);
        $operation['CamelCase'] = CaseTool::snakeToFlexiblePascal($operation['elementName']);
        return $operation;
    }


    /**
     * @param array $operation , a well-formatted operation
     * Executes the given operation, which most of the time (if not always)
     * is of type "create" (generate the morphic files).
     * @throws MorphicException
     */
    protected function executeOperation(array $operation)
    {
        switch ($operation['operationType']) {
            case "create":
                $this->executeCreateOperation($operation);
                break;
            default:
                throw new MorphicException("Unknown operationType: " . $operation['operationType']);
                break;
        }
    }


    protected function executeCreateOperation(array $operation)
    {
        $this->onCreateOperationBefore($operation);
        $formGen = $this->formConfigFileGen;
        if (null === $formGen) {
            throw new MorphicException("Undefined formConfigFileGen variable");
        }
        $content = $formGen->getConfigFileContent($operation, $this->conf);
        $formConfigFileDst = $this->getFormConfigFileDestination($operation, $this->conf);
        FileSystemTool::mkfile($formConfigFileDst, $content);

        /**
         * @todo-ling:
         * main course here...
         */

    }

    protected function onCreateOperationBefore(array $operation)
    {

    }


    protected function getFormConfigFileDestination(array $operation, array $config = [])
    {
        throw new MorphicException("override me");
    }
}