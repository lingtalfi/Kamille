<?php


namespace Kamille\Utils\Morphic\Helper;


class MorphicHelper
{


    public static function getFormContextValue($key, array $context)
    {
        if (array_key_exists($key, $context)) {
            return $context[$key];
        }
        throw new \Exception("Bad assertion: expected key $key to be set in the form context");
    }
}