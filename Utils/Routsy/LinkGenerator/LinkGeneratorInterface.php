<?php


namespace Kamille\Utils\Routsy\LinkGenerator;


interface LinkGeneratorInterface
{
    public function getUri($routeId, array $params = []);
}