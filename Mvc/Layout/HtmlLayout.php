<?php


namespace Kamille\Mvc\Layout;


use Bat\StringTool;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;


class HtmlLayout extends Layout
{


    public function render(array $variables = [])
    {

        ob_start();

        $out = parent::render($variables);


        echo '<!DOCTYPE html>' . PHP_EOL;
        echo '<html' . StringTool::htmlAttributes(HtmlPageHelper::getHtmlTagAttributes()) . '>' . PHP_EOL;
        HtmlPageHelper::displayHead();
        HtmlPageHelper::displayOpeningBodyTag();
        echo $out;
        HtmlPageHelper::displayBodyEndAssets(true);
        echo '</html>' . PHP_EOL;

        return ob_get_clean();
    }


}