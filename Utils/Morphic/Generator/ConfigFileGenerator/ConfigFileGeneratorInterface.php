<?php


namespace Kamille\Utils\Morphic\Generator\ConfigFileGenerator;


interface ConfigFileGeneratorInterface
{
    public function getConfigFileContent(array $operation, array $config = []);
}