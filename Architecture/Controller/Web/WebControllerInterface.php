<?php


namespace Kamille\Architecture\Controller;
use Kamille\Architecture\Response\Web\HttpResponseInterface;


/**
 * A web controller handles a request by returning an http response.
 */
interface WebControllerInterface extends ControllerInterface
{

    /**
     * @return HttpResponseInterface
     */
    public function render();
}