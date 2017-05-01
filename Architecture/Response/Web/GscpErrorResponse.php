<?php


namespace Kamille\Architecture\Response\Web;


class GscpErrorResponse extends HttpResponse
{
    protected function sendContent()
    {
        echo json_encode([
            'type' => 'error',
            'data' => $this->content,
        ]);
    }

}