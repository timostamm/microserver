<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 18.04.18
 * Time: 13:09
 */

namespace TS\Web\Microserver;


use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TS\DependencyInjection\Injector;
use TS\Web\Microserver\Controller\ControllerConfig;
use TS\Web\Microserver\Controller\DIControllerInvoker;
use TS\Web\Microserver\Controller\DIControllerResolver;
use TS\Web\Microserver\Exception\ExceptionFormatter;
use TS\Web\Microserver\Exception\ExceptionHandler;
use TS\Web\Microserver\Exception\HttpException;
use TS\Web\Microserver\Exception\PlaintextExceptionFormatter;
use TS\Web\Microserver\Exception\RoutingExceptionHandler;
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
        $config = new ControllerConfig($routeProvider, $diControllerResolver);
        $this->injector->alias(ParameterConverter::class, SimpleParameterConverter::class);
        $this->injector->alias(ExceptionFormatter::class, PlaintextExceptionFormatter::class);
        $this->injector->alias(ExceptionHandler::class, RoutingExceptionHandler::class);
        $this->configure($this->injector, $config);
    }


    protected function configure(Injector $injector, ControllerConfig $controllerConfig): void
    {
        $controllerConfig->addControllerInstance($this);
    }

    protected function handleException(Exception $ex, Request $request): Response
    {
        /** @var ExceptionHandler $handler */
        $handler = $this->injector->instantiate(ExceptionHandler::class);
        try {
            $response = $handler->handleException($ex, $request);
            if ($response) {
                return $response;
            }
        } catch (HttpException $httpEx) {
            return $this->formatException($httpEx, $request);
        }
        if ($ex instanceof HttpException) {
            return $this->formatException($ex, $request);
        }
        return $this->formatException($ex, $request);
    }

    protected function formatException(Exception $ex, Request $request): Response
    {
        /** @var ExceptionFormatter $formatter */
        $formatter = $this->injector->instantiate(ExceptionFormatter::class);
        if ($ex instanceof HttpException) {
            return $formatter->formatHttpException($ex, $request);
        }
        return $formatter->formatUnhandledException($ex, $request);
    }

}