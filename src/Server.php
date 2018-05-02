<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 18.04.18
 * Time: 13:09
 */

namespace TS\Web\Microserver;


use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TS\DependencyInjection\Injector;
use TS\Web\Microserver\Controller\ControllerConfig;
use TS\Web\Microserver\Controller\DIControllerInvoker;
use TS\Web\Microserver\Controller\DIControllerResolver;
use TS\Web\Microserver\Exception\ExceptionHandler;
use TS\Web\Microserver\Exception\SimpleExceptionHandler;
use TS\Web\Microserver\Routing\ParameterConverter;
use TS\Web\Microserver\Routing\RouteProvider;
use TS\Web\Microserver\Routing\SimpleParameterConverter;


class Server extends AbstractServer
{

    private $injector;

    public function __construct()
    {
        $this->injector = new Injector();
        $routeProvider = new RouteProvider(new AnnotationReader());
        $diControllerResolver = new DIControllerResolver($this->injector);
        $diControllerInvoker = new DIControllerInvoker($this->injector);
        parent::__construct(
            $routeProvider,
            $diControllerResolver,
            $diControllerInvoker
        );
        $routeConfig = new ControllerConfig($routeProvider, $diControllerResolver);
        $this->injector->alias(ParameterConverter::class, SimpleParameterConverter::class);
        $this->injector->alias(ExceptionHandler::class, SimpleExceptionHandler::class);
        $this->configure($this->injector, $routeConfig);
    }


    protected function configure(Injector $injector, ControllerConfig $controllerConfig): void
    {
        $controllerConfig->addControllerInstance($this);
    }

    protected function handleHttpException(HttpException $ex, Request $request): Response
    {
        return $this->injector->instantiate(ExceptionHandler::class)
            ->handleHttpException($ex, $request);
    }


    protected function handleUncaughtException(\Exception $ex, Request $request): Response
    {
        return $this->injector->instantiate(ExceptionHandler::class)
            ->handleUncaughtException($ex, $request);
    }


}