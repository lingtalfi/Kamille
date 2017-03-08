<?php


use Architecture\Application\Web\WebApplication;
use Architecture\Request\Web\HttpRequest;
use Architecture\RequestListener\Web\ControllerExecuterRequestListener;
use Architecture\RequestListener\Web\ResponseExecuterListener;
use Architecture\RequestListener\Web\RouterRequestListener;
use Architecture\Router\Web\StaticPageRouter;



require_once __DIR__ . "/../init.php";


WebApplication::inst()
    ->addListener(RouterRequestListener::create()->addRouter(StaticPageRouter::create()))
    ->addListener(ControllerExecuterRequestListener::create())
    ->addListener(ResponseExecuterListener::create())
    ->handleRequest(HttpRequest::create());

