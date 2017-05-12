<?php


namespace Kamille\Utils\Laws\Theme;


use Kamille\Architecture\Response\Web\HttpResponseInterface;
use Kamille\Utils\Laws\Config\LawsConfig;

interface LawsThemeInterface
{

    public function configureView($viewId, LawsConfig $config);

    /**
     * @param $pageId
     * @return HttpResponseInterface
     */
    public function renderByPageId($pageId);

}