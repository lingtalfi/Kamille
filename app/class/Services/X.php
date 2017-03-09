<?php


namespace Services;

use Architecture\Application\Web\WebApplication;


/**
 * Service container of the application.
 * It contains the services of the application.
 *
 * Services can be added manually or by automates.
 *
 * Note1: remember that this class belongs to the application,
 * so don't hesitate to use it how you like (use php constants if you want).
 * You would just throw it away and restart for a new application, no big deal.
 *
 *
 * Note2: please avoid use statements at the top of this file.
 * I have no particular arguments why, but it makes my head cleaner to
 * see a clean top of the file, thank you by advance, ling.
 *
 *
 *
 *
 *
 */
class X
{


    public static function StaticPageRouter_getStaticPageController()
    {
        $o = new \Architecture\Controller\Web\StaticPageController();
        $o->setPagesDir(WebApplication::inst()->get('app_dir') . "/pages");
        return $o;
    }
}