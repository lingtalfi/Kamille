<?php


namespace Kamille\Utils\ThemeHelper;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;

class KamilleThemeHelper
{
    public static function css($fileName)
    {
        $url = "/theme/" . ApplicationParameters::get("theme") . '/css/' . $fileName;
        HtmlPageHelper::css($url);
    }

    public static function js($fileName)
    {
        $url = "/theme/" . ApplicationParameters::get("theme") . '/js/' . $fileName;
        HtmlPageHelper::js($url);
    }
}