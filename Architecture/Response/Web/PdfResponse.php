<?php


namespace Kamille\Architecture\Response\Web;


class PdfResponse extends HttpResponse
{

    public function __construct($content = "", $code = 200)
    {
        parent::__construct($content, $code);
        header('Content-Type: application/pdf');
    }


}