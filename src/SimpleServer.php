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
use TS\Web\Microserver\Controller\SimpleControllerInvoker;
use TS\Web\Microserver\Controller\SimpleControllerResolver;
use TS\Web\Microserver\Exception\HttpException;
use TS\Web\Microserver\Exception\PlaintextExceptionFormatter;
use TS\Web\Microserver\Exception\RoutingExceptionHandler;
use TS\Web\Microserver\Routing\RouteProvider;


class SimpleServer extends AbstractServer
{

    private $exceptionHandler;
    private $exceptionFormatter;

    public function __construct()
    {
        parent::__construct(new RouteProvider(new AnnotationReader()), new SimpleControllerResolver(), new SimpleControllerInvoker());
        $this->exceptionFormatter = new PlaintextExceptionFormatter(true);
        $this->exceptionHandler = new RoutingExceptionHandler();
    }

    public function addController(... $class): void
    {
        foreach ($class as $c) {
            $this->routeProvider->addControllerClass($c);
        }
    }

    protected function handleException(\Exception $ex, Request $request): Response
    {
        try {
            $response = $this->exceptionHandler->handleException($ex, $request);
            if ($response) {
                return $response;
            }
        } catch (HttpException $httpEx) {
            return $this->exceptionFormatter->formatHttpException($httpEx, $request);
        }
        if ($ex instanceof HttpException) {
            return $this->exceptionFormatter->formatHttpException($ex, $request);
        }
        return $this->exceptionFormatter->formatUnhandledException($ex, $request);
    }

}