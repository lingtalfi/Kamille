<?php


namespace Kamille\Utils\Morphic\Util\ModuleConfiguration;


use Bat\SessionTool;
use Kamille\Utils\Morphic\Helper\MorphicHelper;
use QuickPdo\QuickPdo;
use SokoForm\Control\SokoInputControl;
use SokoForm\Form\SokoFormInterface;

class MorphicModuleConfigurationUtil
{

    private static $configurationEntries = null;


    protected $configurationTable;
    protected $textSuccessUpdate;
    protected $controlMap;


    public function __construct()
    {
        $this->configurationTable = "mymodule_configuration";
        $this->textSuccessUpdate = "Les valeurs de configuration ont bien été mises à jour";
        $this->controlMap = [];
    }


    public static function create()
    {
        return new static();
    }

    public function setTableName(string $configurationTable)
    {
        $this->configurationTable = $configurationTable;
        return $this;
    }


    public function decorateSokoFormInstance(SokoFormInterface $form)
    {
        $rows = $this->getConfigurationEntries();


        foreach ($rows as $row) {


            // depending on the type we can have a boolean or other controls...
            $type = $row['type'];
            $typeParams = $row['type_params'];
            $key = $row['the_key'];
            $value = $row['the_value'];
            $label = $row['label'];
            $description = $row['description'];


            // choose the control
            $getControlCallback = $this->controlMap[$type] ?? null;
            if (null !== $getControlCallback) {
                $control = call_user_func($getControlCallback, $typeParams);
            } else {
                $control = SokoInputControl::create();

            }


            // common control properties
            $control
                ->setName($key)
                ->setLabel($label)
                ->addProperties([
                    "info" => $description,
                ])
                ->setValue($value);


            // add the control
            $form->addControl($control);
        }
    }

    public function getFeedFunction()
    {
        return function (SokoFormInterface $form, array $ric) {
            if (SessionTool::pickupFlag("form-module_configuration")) {
                $form->addNotification($this->textSuccessUpdate, "success");
            }


            // @todo-ling: if required?
        };
    }

    public function getProcessFunction()
    {
        return function ($fData, SokoFormInterface $form) {

            $entries = $this->getConfigurationEntries();
            foreach ($entries as $entry) {
                $theKey = $entry['the_key'];
                if (array_key_exists($theKey, $fData)) {
                    $value = $fData[$theKey];
                    QuickPdo::update($this->configurationTable, [
                        "the_value" => $value,
                    ], [
                        ["the_key", "=", $theKey],
                    ]);
                }
            }


            /**
             * We redirect to refresh the tree
             */
            SessionTool::setFlag("form-module_configuration");
            MorphicHelper::redirect();
            return false;
        };
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getConfigurationEntries()
    {
        if (null === self::$configurationEntries) {
            self::$configurationEntries = QuickPdo::fetchAll("select * from $this->configurationTable");
        }
        return self::$configurationEntries;
    }

}