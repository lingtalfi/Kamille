<?php


namespace Kamille\Utils\Routsy\LinkGenerator;


use Bat\UriTool;

/**
 * This is a service to generate links, provided by the framework, for the kamille developers/users.
 *
 *
 * Usage
 * ============
 * ApplicationLinkGenerator::getUri("Core_myRouteId5", [
 *      'dynamic' => 46,
 * ]);
 *
 * See Routsy system documentation for more information.
 *
 */
class ApplicationLinkGenerator
{
    /**
     * @var $linkGen LinkGeneratorInterface
     */
    private static $linkGen;
    public static $defaultHttps = false;


    public static function getUri($routeId, array $params = [], $absolute = false, $https = null)
    {
        // note: we don't do a condition here to save an if
        // if you thinks that's ridiculous, by all means change this implementation
        $ret = self::$linkGen->getUri($routeId, $params);
        if (true === $absolute) {
            if (null === $https) {
                $https = self::$defaultHttps;
            }
            $protocol = (true === $https) ? 'https' : 'http';
            $host = UriTool::getHost();
            $ret = $protocol . "://" . $host . $ret;
        }
        return $ret;
    }


    public static function setLinkGenerator(LinkGeneratorInterface $linkGenerator)
    {
        self::$linkGen = $linkGenerator;
    }
}