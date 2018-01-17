<?php


namespace Kamille\Utils\Morphic\ListConfigurationProvider;

use Kamille\Utils\Morphic\Exception\MorphicException;


/**
 * This configurationProvider uses files
 * to store the configuration of the lists.
 */
class ListConfigurationProvider implements ListConfigurationProviderInterface
{


    private $confDir;


    public function __construct()
    {
        $this->confDir = null;
    }

    public static function create()
    {
        return new static();
    }

    public function setConfDir($confDir)
    {
        $this->confDir = $confDir;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function getConfig($module, $identifier)
    {
        $file = $this->confDir . "/$module/$identifier.conf.php";
        $conf = [];
        if (file_exists($file)) {
            include $file;
        } else {
            throw new MorphicException("File not found: $file");
        }
        return $conf;
    }
}