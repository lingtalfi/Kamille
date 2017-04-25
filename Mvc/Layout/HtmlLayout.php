<?php


namespace Kamille\Mvc\Layout;


use Bat\StringTool;
use Core\Services\X;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;


class HtmlLayout extends Layout
{


    public function render(array $variables = [])
    {

        ob_start();

        $out = parent::render($variables);



        //--------------------------------------------
        // INJECTING LAZY JS CODE AT THE END OF THE BODY
        //--------------------------------------------
        if (null !== ($coll = X::get("Core_lazyJsInit", null, false))) {
            /**
             * @var $coll \Module\Core\JsLazyCodeCollector\JsLazyCodeCollectorInterface
             */
            HtmlPageHelper::addBodyEndSnippet($coll->getCompiledJsCode());
        }

        echo '<!DOCTYPE html>' . PHP_EOL;
        echo '<html' . StringTool::htmlAttributes(HtmlPageHelper::getHtmlTagAttributes()) . '>' . PHP_EOL;
        HtmlPageHelper::displayHead();
        HtmlPageHelper::displayOpeningBodyTag();
        echo $out;
        HtmlPageHelper::displayBodyEndSection(true);
        echo '</html>' . PHP_EOL;

        return ob_get_clean();
    }


}