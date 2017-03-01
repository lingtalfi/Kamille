<?php


namespace Architecture\RequestListener;


use Architecture\Request\HttpRequestInterface;

interface HttpRequestListenerInterface extends RequestListenerInterface
{

    public function listen(HttpRequestInterface $request);
}