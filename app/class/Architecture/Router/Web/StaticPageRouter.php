<?php


namespace Architecture\Router\Web;


use Architecture\Request\Web\HttpRequestInterface;
use Architecture\Router\RouterInterface;
use Services\Hooks;

class StaticPageRouter implements RouterInterface
{
    private $uri2Page;

    public function __construct()
    {
        $this->uri2Page = [];
    }

    public static function create()
    {
        return new static();
    }
    //--------------------------------------------
    //
    //--------------------------------------------
    public function match(HttpRequestInterface $request)
    {
        $uri = $request->uri(false);
        $uri2Page = $this->uri2Page;
        Hooks::StaticPageRouter_feedRequestUri($uri2Page);
        if (array_key_exists($uri, $uri2Page)) {
            $page = $uri2Page[$uri];
            return [
                'Architecture\Controller\Web\StaticPageController:handlePage',
                [
                    'page' => $page,
                ],
            ];
        }
    }


}