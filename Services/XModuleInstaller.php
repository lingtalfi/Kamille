<?php


namespace Kamille\Services;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\KamilleNaiveImporter\Importer\KamilleModulesImporter;
use Kamille\Utils\KamilleNaiveImporter\KamilleNaiveImporter;
use Kamille\Utils\KamilleNaiveImporter\KamilleNaiveImporterInterface;
use Output\WebProgramOutput;

class XModuleInstaller
{

    private static $inst;


    /**
     * @return KamilleNaiveImporterInterface
     */
    public static function inst()
    {
        if (null === self::$inst) {
            $appDir = ApplicationParameters::get("app_dir");
            $output = WebProgramOutput::create();
            self::$inst = KamilleNaiveImporter::create()
                ->setOutput($output)
                ->setAppDir($appDir)
                ->addImporter(KamilleModulesImporter::create()->setAliases(['km']));
        }
        return self::$inst;
    }


    public static function isInstalled($moduleName)
    {
        return self::inst()->isInstalled($moduleName);
    }

    public static function getInstalled()
    {
        return self::inst()->getInstalledModulesList();
    }


}