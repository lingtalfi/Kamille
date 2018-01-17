<?php


namespace Kamille\Utils\Morphic\ListRenderer;


use Kamille\Mvc\Theme\Theme;
use QuickPdo\Util\QuickPdoListInfoUtil;

class MorphicAdminListRenderer
{
    private $widgetRendererIdentifier;


    public function __construct()
    {
        $this->widgetRendererIdentifier = null;
    }


    public static function create()
    {
        return new static();
    }


    public function setWidgetRendererIdentifier($widgetRendererIdentifier)
    {
        $this->widgetRendererIdentifier = $widgetRendererIdentifier;
        return $this;
    }


    public function renderByConfig(array $config)
    {

        //--------------------------------------------
        //
        //--------------------------------------------
        $util = QuickPdoListInfoUtil::create()
            ->setQuerySkeleton($config['querySkeleton'])
            ->setQueryCols($config['queryCols']);
        if (null !== $config['allowedFilters']) {
            $util->setAllowedFilters($config['allowedFilters']);
        }
        if (null !== $config['allowedSort']) {
            $util->setAllowedSorts($config['allowedSort']);
        }


        $info = $util->execute([
            'sort' => $config['sort'],
            'filters' => $config['filters'],
            'nipp' => $config['nipp'],
            'page' => $config['page'],
        ]);


        $rows = $info['rows'];
        $renderer = Theme::getWidgetRenderer($this->widgetRendererIdentifier);
        $renderer->setModel([
            'title' => $config['title'],
            'rows' => $rows,
            'page' => $info['page'],
            'nbPages' => $info['nbPages'],
            'nipp' => $info['nipp'],
            'nbItems' => $info['nbItems'],
            'nippChoices' => $config['nippChoices'],
        ]);
        return $renderer->render();
    }

}