<?php


namespace Mvc\Renderer;

use Mvc\Renderer\Exception\RendererException;

/**
 * In this class, to interpret the uninterpreted php content,
 * I chose to create a tmp file first, because it's easier to debug.
 *
 */
class PhpLayoutRenderer extends LayoutRenderer
{

    protected $vars; // I use this mechanism to avoid variable name collision


    public function render($uninterpretedContent, array $variables)
    {

        $this->vars = $variables;


        if (false !== ($path = $this->tmpFile($uninterpretedContent))) {


            /**
             * Convert all variables accessible as objects.
             * (i.e. $v->my_var withing the template)
             *
             */
            $v = json_decode(json_encode($variables), false);
            $l = $this->layout;


            /**
             * First interpret the template's php if any
             */
            ob_start();
            include $path;
            $content = ob_get_clean();


            /**
             * Then replace tags
             */
            $this->replaceTags($content);


            return $content;

        } else {
            throw new RendererException("Cannot create the temporary file to create content");
        }
    }


    protected function replaceTags(&$content)
    {
        /**
         * Prepare vars
         */
        $varsKeys = [];
        $varsValues = [];
        foreach ($this->vars as $k => $v) {
            if (!is_array($v)) {
                $varsKeys[] = '{' . $k . '}';
                $varsValues[] = $v;
            }
        }
        $content = str_replace($varsKeys, $varsValues, $content);
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function tmpFile($content)
    {
        $tmpfname = tempnam("/tmp/PhpLayoutRenderer", "FOO");
        file_put_contents($tmpfname, $content);
        return $tmpfname;
    }
}