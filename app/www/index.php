<?php


use Architecture\Application\Web\WebApplication;
use Architecture\Request\Web\HttpRequest;

require_once __DIR__ . "/../init.php";



WebApplication::inst()
    ->addListener(RouterRequestListener::create())
    ->handleRequest(HttpRequest::create());