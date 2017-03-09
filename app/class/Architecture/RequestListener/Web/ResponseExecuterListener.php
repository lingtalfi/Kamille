<?php


namespace Architecture\RequestListener\Web;


use Architecture\Request\Web\HttpRequestInterface;
use Architecture\Response\Web\HttpResponseInterface;
use Architecture\Router\RouterInterface;


/**
 * This class checks whether or not a response property is in the Request.
 * If so, it supposes that it's an HttpResponse, and it executes the response.
 */
class ResponseExecuterListener implements HttpRequestListenerInterface
{

    public static function create()
    {
        return new static();
    }

    public function listen(HttpRequestInterface $request)
    {
        if (null !== ($response = $request->get("response"))) {
            if ($response instanceof HttpResponseInterface) {
                $response->send();
            }
        }
    }
}