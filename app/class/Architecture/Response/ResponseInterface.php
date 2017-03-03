<?php


namespace Architecture\Response;

/**
 * The response is a response to a request.
 * So, the send method sends what's appropriate to answer the request.
 */
interface ResponseInterface
{
    public function send();
}