<?php


use Kamille\Ling\Z;

use Kamille\Mvc\Layout\Layout;
use Kamille\Mvc\Loader\FileLoader;
use Kamille\Mvc\Renderer\PhpLayoutRenderer;
use Kamille\Mvc\Widget\Widget;

require_once __DIR__ . "/../init.php";


$wloader = FileLoader::create()->addDir(Z::appDir() . "/theme/widget");
$commonRenderer = PhpLayoutRenderer::create();

echo Layout::create()
    ->setTemplate("home")
    ->setLoader(FileLoader::create()
        ->addDir(Z::appDir() . "/theme/layout")
    )
    ->setRenderer($commonRenderer)
    ->bindWidget("meteo", Widget::create()
        ->setTemplate("meteo")
        ->setVariables(['level' => "good"])
        ->setLoader($wloader)
        ->setRenderer($commonRenderer)
    )
    ->render([
        "name" => 'Pierre',
    ]);




