<?php




require_once __DIR__ . "/../init.php";




$controller = '\Architecture\Controller\Web\StaticPageController';
call_user_func([$controller, 'handlePage'], "pou");