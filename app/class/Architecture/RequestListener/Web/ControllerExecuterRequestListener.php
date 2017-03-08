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
        if (is_callable($controller)) {

            $response = null;


            /**
             * Here, we try to get the parameters from the controller callback,
             * so we know that a controller is a callable, but there are many forms of
             * callable (http://php.net/manual/en/language.types.callable.php):
             *
             *
             * - string                                     (simple function name)
             * - string                                     ('MyClass::myCallbackMethod') as of PHP 5.2.3
             * - array [$o, "methodName"]                   (object method call)
             * - array ["MyClass", "methodName"]            (static method call)
             *
             * - ...other methods are available, but I'm not implementing them here
             *
             */
            if (is_array($controller) && array_key_exists(0, $controller) && array_key_exists(1, $controller)) {


                $response = call_user_func_array($controller, $controllerParams);



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



            }
            elseif(is_string($controller)){

            }
            else {
                throw new RequestListenerException("Unknown form of callable, sorry");
            }


            return $response;


        } else {
            /**
             * Here, we throw an exception.
             * We assume that kamille users use the system with 3 request listeners: router, controller, response.
             */
            $type = gettype($controller);
            throw new RequestListenerException("Controller should be a callable, $type given");
        }


    }

}