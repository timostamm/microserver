<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 18.04.18
 * Time: 13:09
 */

namespace TS\Web\Microserver;


use Doctrine\Common\Annotations\AnnotationReader;
use TS\Web\Microserver\Controller\SimpleControllerInvoker;
use TS\Web\Microserver\Controller\SimpleControllerResolver;
use TS\Web\Microserver\Exception\SimpleExceptionHandler;
use TS\Web\Microserver\Routing\RouteProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SimpleServer extends AbstractServer
{

    private $exceptionHandler;

    public function __construct()
    {
        parent::__construct(new RouteProvider(new AnnotationReader()), new SimpleControllerResolver(), new SimpleControllerInvoker());
        $this->exceptionHandler = new SimpleExceptionHandler(true);
    }

    public function addController(... $class): void
    {
        foreach ($class as $c) {
            $this->routeProvider->addControllerClass($c);
        }
    }


    protected function handleHttpException(HttpException $ex, Request $request): Response
    {
        return $this->exceptionHandler->handleHttpException($ex, $request);
    }


    protected function handleUncaughtException(\Exception $ex, Request $request): Response
    {
        return $this->exceptionHandler->handleUncaughtException($ex, $request);
    }


}