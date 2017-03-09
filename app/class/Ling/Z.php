<?php


namespace Ling;


use Architecture\Application\Web\WebApplication;
use Architecture\Request\Web\HttpRequestInterface;

/**
 * Trying to reduce the developer typing.
 *
 * This class was created in an environment with three RequestListeners (Router, Controller, Response),
 * as described in the kam framework.
 *
 */
class Z
{


    public static function getUrlParam($key, $defaultValue = null, $throwEx = false)
    {
        $request = self::request(null, true);
        $u = $request->get('urlParams', []);
        if (array_key_exists($key, $u)) {
            return $u[$key];
        }
        if (true === $throwEx) {
            throw new LingException("urlParam not set: $key");
        }
        return $defaultValue;
    }


    /**
     * @return HttpRequestInterface
     */
    public static function request($defaultValue = null, $throwEx = false)
    {
        $request = WebApplication::inst()->get('request');
        if (null !== $request) {
            return $request;
        }
        if (true === $throwEx) {
            throw new LingException("request is not set yet (called to soon?)");
        }
        return $defaultValue;
    }
}