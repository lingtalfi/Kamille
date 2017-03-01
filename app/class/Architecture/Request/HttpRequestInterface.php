<?php


namespace Architecture\Request;


interface HttpRequestInterface extends RequestInterface
{
    /**
     * @return string: uri, starts with a slash (/)
     */
    public function getUri($withQueryString = true);
}