<?php


namespace Architecture\Controller\Web;


use Architecture\Controller\ControllerInterface;

class StaticPageRouter implements ControllerInterface
{

    private $pagesDir;

    public function handlePage($page)
    {
        $file = $this->pagesDir . "/" . $page;
        if(file_exists($file)){

        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function setPagesDir($pagesDir)
    {
        $this->pagesDir = $pagesDir;
        return $this;
    }
}