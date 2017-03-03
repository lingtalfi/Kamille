<?php


namespace Architecture\RequestListener\Web;


use Architecture\Request\Web\HttpRequestInterface;
use Architecture\RequestListener\Exception\RequestListenerException;
use Architecture\Response\Web\HttpResponseInterface;
use Architecture\Router\RouterInterface;


/**
 * This requestListener sees if a response is already set in the Request.
 * If not, looks for a controller parameter in the Request.
 * If a controller parameter is found in the Request parameters, then this class
 * executes the corresponding Controller.
 *
 * Arguments of the controller method are/can be passed via the controllerParams parameter of the Request.
 *
 * This executor attaches a Response to the Request, if a Response was returned by the Controller.
 *
 * If the Controller doesn't return a proper Response, an exception is thrown.
 *
 */
class ControllerExecuterRequestListener implements HttpRequestListenerInterface
{


    public static function create()
    {
        return new static();
    }

    public function listen(HttpRequestInterface $request)
    {
        $controller = $request->get("controller", null);
        $controllerParams = $request->get("controllerParams", []);
        $response = $this->executeController($controller, $controllerParams);
        if ($response instanceof HttpResponseInterface) {
            $request->set("response", $response);
        } else {
            throw new RequestListenerException("A controller should return a response");
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function executeController($controller, array $controllerParams = [])
    {
        if (is_string($controller)) {
            list($class, $method) = $this->getControllerClassAndMethod($controller);

            $r = new \ReflectionMethod($class, $method);
            $rp = $r->getParameters();
            $params = [];
            foreach ($rp as $parameter) {
                $name = $parameter->getName();
                if (array_key_exists($name, $controllerParams)) {
                    $params[] = $controllerParams[$name];
                } else {
                    if (false === $parameter->isOptional()) {
                        throw new RequestListenerException("non option parameter $name was required by the controller $controller");
                    }
                }
            }


            $response = call_user_func_array([$class, $method], $params);
//            a($r->invokeArgs($class, $params));


        } else {
            /**
             * A technical possibility is that the controller could directly be a callable (instead of a string).
             * But this is not implemented yet (the idea has to be validated first).
             *
             * Note: the idea is not accepted yet because it's easier to debug when a string is transported
             * via the Request (rather than a callable). Imagine you want to ask the Request:
             * what controller will be executed?
             */
        }


    }

    private function getControllerClassAndMethod($controllerString)
    {
        $p = explode(":", $controllerString, 2);
        if (2 === count($p)) {
            return [$p[0], $p[1]];
        }
        throw new RequestListenerException("Malformed controllerString: $controllerString");
    }
}