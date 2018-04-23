<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 18.04.18
 * Time: 13:09
 */

namespace TS\Web\Microserver;


use Doctrine\Common\Annotations\AnnotationReader;
use TS\Web\Microserver\Controller\DIControllerInvoker;
use TS\Web\Microserver\Controller\DIControllerResolver;
use TS\Web\Microserver\Exception\ExceptionHandler;
use TS\Web\Microserver\Exception\SimpleExceptionHandler;
use TS\Web\Microserver\Routing\ParameterConverter;
use TS\Web\Microserver\Routing\RouteProvider;
use TS\Web\Microserver\Routing\SimpleParameterConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TS\DependencyInjection\Injector;
use TS\DependencyInjection\InjectorInterface;
use TS\DependencyInjection\InspectableInjectorInterface;


class Server extends AbstractServer
{

    private $injector;

    public function __construct()
    {
        $this->injector = new Injector();
        parent::__construct(
            new RouteProvider(new AnnotationReader()),
            $this->injector->instantiate(DIControllerResolver::class, [
                InjectorInterface::class => $this->injector
            ]),
            $this->injector->instantiate(DIControllerInvoker::class, [
                InspectableInjectorInterface::class => $this->injector
            ])
        );
        $this->injector->alias(ParameterConverter::class, SimpleParameterConverter::class);
        $this->injector->alias(ExceptionHandler::class, SimpleExceptionHandler::class);
        $this->routeProvider->addControllerClass(get_class($this));
        $this->controllerResolver->setControllerInstance(get_class($this), $this);
        $this->configure($this->injector);
    }


    protected function configure(Injector $injector):void
    {
    }


    public function addController(... $class): void
    {
        foreach ($class as $c) {
            $this->routeProvider->addControllerClass($c);
        }
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