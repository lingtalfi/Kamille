<?php


namespace Kamille\Mvc\ThemeWidget;

use Kamille\Mvc\ThemeWidget\Exception\ThemeWidgetException;
use Kamille\Mvc\ThemeWidget\Renderer\ThemeWidgetRendererInterface;


/**
 * To use this class,
 * override it in your app (call it Theme, for instance)
 * and create the methods you want in it.
 *
 * For instance if you create a protected method called: Ekom_Back_GuiAdminRenderer,
 * which MUST return a ThemeWidgetRendererInterface.
 *
 * Then, you can call this method using the getWidgetRenderer method, passing the
 * Ekom_Back_GuiAdminRenderer as the identifier.
 *
 *
 *
 */
class AbstractTheme
{

    /**
     * @param $identifier
     * @return ThemeWidgetRendererInterface
     * @throws ThemeWidgetException
     */
    public static function getWidgetRenderer($identifier)
    {
        if (method_exists(get_called_class(), $identifier)) {
            $ret = call_user_func_array([static::class, $identifier]);
            if ($ret instanceof ThemeWidgetRendererInterface) {
                return $ret;
            } else {
                throw new ThemeWidgetException("method $identifier should return a ThemeWidgetRendererInterface object");
            }
        } else {
            throw new ThemeWidgetException("method not found with identifier $identifier");
        }

    }
}