<?php


namespace Kamille\Utils\RoutsyCopy\LinkGenerator;


interface LinkGeneratorInterface
{
    public function getUri($routeId, array $params = []);
}