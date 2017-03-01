<?php


namespace Architecture\Application;




use Architecture\Request\HttpRequestInterface;

interface WebApplicationInterface extends ApplicationInterface
{
    public function handleRequest(HttpRequestInterface $request);
}