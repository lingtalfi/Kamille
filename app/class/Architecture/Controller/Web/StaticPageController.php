<?php


namespace Architecture\Controller\Web;


use Architecture\Controller\ControllerInterface;
use Architecture\Controller\Exception\ControllerException;
use Architecture\Response\Web\HttpResponse;
use Architecture\Response\Web\HttpResponseInterface;

class StaticPageController implements ControllerInterface
{

    private $pagesDir;

    public function handlePage($page)
    {
        $file = $this->pagesDir . "/" . $page;
        if (file_exists($file)) {
            $response = $this->getFileContent($file);
            if ($response instanceof HttpResponseInterface) {
                while (ob_get_level()) {
                    ob_end_clean();
                }
                return $response;
            } else {
                throw new ControllerException("Controller did not return an HttpResponseInterface with page $page");
            }
        } else {
            throw new ControllerException("File not found: $file, for page $page");
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

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getFileContent($file)
    {
        ob_start();
        /**
         * Note: from inside file, you can return a Response directly (RedirectResponse, DownloadResponse, ...)
         */
        include $file;
        $s = ob_get_clean();
        return HttpResponse::create($s);
    }

}