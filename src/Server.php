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
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TS\DependencyInjection\Injector;
use TS\DependencyInjection\InspectableInjectorInterface;
use TS\Web\Microserver\Controller\ControllerConfig;
use TS\Web\Microserver\Controller\DIControllerInvoker;
use TS\Web\Microserver\Controller\DIControllerResolver;
use TS\Web\Microserver\Controller\SimpleControllerResolver;
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

    public function __construct(InspectableInjectorInterface $injector = null)
    {
        $this->injector = $injector ?? new Injector();

        parent::__construct(
            new RouteProvider(new AnnotationReader()),
            new DIControllerResolver($this->injector),
            new DIControllerInvoker($this->injector)
        );

        $config = $this->injector->instantiate(ControllerConfig::class, [
            RouteProvider::class => $this->routeProvider,
            SimpleControllerResolver::class => $this->controllerResolver
        ]);

        $this->configure($this->injector, $config);
    }


    protected function configure(Injector $injector, ControllerConfig $controllerConfig): void
    {

        if (! $this->injector->has(ParameterConverter::class)) {
            $injector->alias(ParameterConverter::class, SimpleParameterConverter::class);
        }
        if (! $this->injector->has(ExceptionFormatter::class)) {
            $injector->alias(ExceptionFormatter::class, PlaintextExceptionFormatter::class, [
                '$includeDetails' => true
            ]);
        }
        if (! $this->injector->has(ExceptionHandler::class)) {
            $injector->alias(ExceptionHandler::class, RoutingExceptionHandler::class);
        }
        if (! $this->injector->has(UrlGeneratorInterface::class)) {
            $injector->factory(UrlGeneratorInterface::class, function (){
                if (!$this->urlGenerator) {
                    $this->urlGenerator = new UrlGenerator($this->getRouteCollection(), $this->requestContext);
                }
                return $this->urlGenerator;
            });
        }

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

    protected function getUrlGenerator():UrlGeneratorInterface
    {
        return $this->injector->instantiate(UrlGeneratorInterface::class);
    }


}