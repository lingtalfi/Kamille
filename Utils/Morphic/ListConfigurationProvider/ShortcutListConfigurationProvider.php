<?php


namespace Kamille\Utils\Morphic\ListConfigurationProvider;

use Kamille\Utils\Morphic\Exception\MorphicException;


/**
 * This configurationProvider uses files
 * to store the configuration of the lists.
 */
class ShortcutListConfigurationProvider extends ListConfigurationProvider
{

    //--------------------------------------------
    //
    //--------------------------------------------
    public function getConfig($module, $identifier)
    {
        $file = $this->confDir . "/$module/$identifier.list.conf.php";
        $defaultFile = $this->confDir . "/$module/_default.list.conf.php";
        $conf = [];
        if (file_exists($file)) {
            include $file;


            /**
             * Merge the conf with either the user provided default conf,
             * or the default conf provided by this planet.
             */
            $ric = $conf['ric'];
            $formRoute = $conf['formRoute'];
            if (false === file_exists($defaultFile)) {
                $defaultFile = __DIR__ . "/../assets/list/_default.list.conf.php";
            }
            $defaultConf = [];
            include $defaultFile;
            foreach ($defaultConf as $k => $v) {
                if (false === array_key_exists($k, $conf)) {
                    $conf[$k] = $v;
                }
            }
        } else {
            throw new MorphicException("File not found: $file");
        }
        return $conf;
    }
}