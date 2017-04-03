<?php


namespace Kamille\Architecture\Controller\Web;


use Kamille\Architecture\Controller\ControllerInterface;
use Kamille\Architecture\Response\Web\HttpResponse;
use Kamille\Architecture\Response\Web\HttpResponseInterface;
use Kamille\Utils\Laws\LawsUtil;


/**
 * This controller implements standard techniques promoted by the kamille framework
 */
class KamilleController implements ControllerInterface
{


    /**
     * Renders a laws view.
     * More info on laws here: https://github.com/lingtalfi/laws
     *
     * @return HttpResponseInterface
     */
    protected function renderByViewId($viewId)
    {
        return HttpResponse::create(LawsUtil::renderLawsViewById($viewId));
    }

}