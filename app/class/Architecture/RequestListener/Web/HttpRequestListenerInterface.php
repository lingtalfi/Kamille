<?php


namespace Architecture\RequestListener\Web;



use Architecture\Request\Web\HttpRequestInterface;
use Architecture\RequestListener\RequestListenerInterface;


interface HttpRequestListenerInterface extends RequestListenerInterface
{

    public function listen(HttpRequestInterface $request);
}