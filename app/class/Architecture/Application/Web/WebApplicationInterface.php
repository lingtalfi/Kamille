<?php


namespace Architecture\Application\Web;




use Architecture\Application\ApplicationInterface;
use Architecture\Request\Web\HttpRequestInterface;


interface WebApplicationInterface extends ApplicationInterface
{
    public function handleRequest(HttpRequestInterface $request);
}