<?php


namespace Architecture\RequestListener\Web;


use Architecture\Request\Web\HttpRequestInterface;


/**
 * This is a router for a web application.
 *
 * It sets the controller parameter in the request (if a route matches),
 * or do nothing special otherwise.
 *
 */
class RouterRequestListener implements HttpRequestListenerInterface
{



    public function listen(HttpRequestInterface $request)
    {

    }

}