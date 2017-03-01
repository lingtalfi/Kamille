<?php



namespace Architecture\RequestListener;


use Architecture\Request\RequestInterface;

interface RequestListenerInterface{

    /**
     * Do something with the given request.
     * Note: it's commented, so that if you create an interface which extends this one,
     * you can use a more specific RequestInterface.
     */
    // public function listen(RequestInterface $request);
}