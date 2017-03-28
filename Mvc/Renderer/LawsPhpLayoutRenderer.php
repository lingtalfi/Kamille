<?php


namespace Kamille\Mvc\Renderer;


use Kamille\Mvc\LayoutProxy\LawsLayoutProxy;

class LawsPhpLayoutRenderer extends PhpLayoutRenderer
{


    protected function getLayoutProxy()
    {
        if (null === $this->layoutProxy) {
            $this->layoutProxy = new LawsLayoutProxy();
            $this->layoutProxy->setLayout($this->layout);
        }
        return $this->layoutProxy;
    }
}