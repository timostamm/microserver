<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 18.04.18
 * Time: 15:00
 *
 * This file was copied and modified from the Symfony package.
 * License: MIT
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 */

namespace TS\Web\Microserver\Controller;


use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TS\DependencyInjection\Exception\InjectionException;
use TS\DependencyInjection\Injector\ArgumentInspectionInterface;
use TS\DependencyInjection\InspectableInjectorInterface;
use TS\Web\Microserver\HttpException;
use TS\Web\Microserver\Routing\ParameterConverter;

class DIControllerInvoker implements ControllerInvokerInterface
{

    protected $injector;

    public function __construct(InspectableInjectorInterface $injector)
    {
        $this->injector = $injector;
    }


    protected function buildParams(ArgumentInspectionInterface $inspection, ParameterConverter $converter, Request $request, Response $response):array
    {
        $route_params = $request->attributes->get('_route_params', []);
        $params = [];
        foreach (array_merge($inspection->getMissing(), $inspection->getOptional()) as $name) {
            if ($inspection->getType($name) === Request::class) {
                $params['$' . $name] = $request;
            }
            if ($inspection->getType($name) === Response::class) {
                $params['$' . $name] = $response;
            }
            if (isset($route_params[$name])) {
                $params['$' . $name] = $converter->convert($name, $route_params[$name], $inspection->getType($name));
            }
        }
        return $params;
    }


    public function invoke(callable $controller, Request $request, Response $response):Response
    {
        $inspection = $this->injector->inspectInvocation($controller);
        $converter = $this->injector->instantiate(ParameterConverter::class);
        $params = $this->buildParams($inspection, $converter, $request, $response);

        try {

            $response = $this->injector->invoke($controller, $params);

            if(! $response instanceof Response) {
                if ($request->attributes->has('_controller')) {
                    $msg = sprintf('The controller %s did not return a %s instance.',  $request->attributes->get('_controller'), Response::class);
                } else if ( $request->attributes->has('_route') ) {
                    $msg = sprintf('The route %s did not return a %s instance.',  $request->attributes->get('_route'), Response::class);
                }
                throw new LogicException($msg);
            }
            return $response;

        } catch (InjectionException $exception) {

            throw new HttpException(Response::HTTP_BAD_REQUEST, $exception->getMessage(), $exception);
        }

    }



}