<?php


namespace Kamille\Architecture\Response\Web;


class GscpSuccessResponse extends HttpResponse
{
    protected function sendContent()
    {
        echo json_encode([
            'type' => 'success',
            'data' => $this->content,
        ]);
    }

}