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
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use TS\Web\Microserver\Controller\ControllerInvoker;
use TS\Web\Microserver\Controller\ControllerInvokerInterface;
use TS\Web\Microserver\Controller\ControllerResolver;
use TS\Web\Microserver\Controller\ControllerResolverInterface;
use TS\Web\Microserver\Exception\HttpException;
use TS\Web\Microserver\Routing\RouteProvider;
use TS\Web\Microserver\Routing\RouteProviderInterface;


class Server
{

    protected $includeExceptionDetails;
    protected $routeProvider;
    protected $controllerResolver;
    protected $controllerInvoker;
    protected $requestContext;
    protected $urlGenerator;
    private $routes = null;


    public function __construct(bool $includeExceptionDetails = true, RouteProviderInterface $routeProvider = null, ControllerResolverInterface $controllerResolver = null, ControllerInvokerInterface $controllerInvoker = null)
    {
        $this->includeExceptionDetails = $includeExceptionDetails;
        $this->routeProvider = $routeProvider ?? new RouteProvider(new AnnotationReader());
        $this->controllerResolver = $controllerResolver ?? new ControllerResolver();
        $this->controllerInvoker = $controllerInvoker ?? new ControllerInvoker();
        $this->requestContext = new RequestContext();
    }


    public function addController(string $class, callable $factory = null): void
    {
        if (!$this->routeProvider instanceof RouteProvider) {
            throw new \LogicException('Route provider must extend ' . RouteProvider::class . ' in order to use addController().');
        }
        $this->routeProvider->addControllerClass($class);
        if ($factory) {
            if (!$this->controllerResolver instanceof ControllerResolver) {
                throw new \LogicException('Controller resolver must extend ' . ControllerResolver::class . ' in order to use addController() with factory.');
            }
            $this->controllerResolver->setControllerFactory($class, $factory);
        }
    }


    public function serve(Request $request = null): Response
    {
        if (!$request) {
            $request = Request::createFromGlobals();
        }

        try {

            $this->requestContext->fromRequest($request);

            $this->matchRequest($request);

            $controller = $this->controllerResolver->getController($request);

            $response = new Response();

            $response = $this->controllerInvoker->invoke($controller, $request, $response);

            return $response;

        } catch (Exception $ex) {
            try {
                $response = $this->handleException($ex, $request);
                if ($response) {
                    return $response;
                }
            } catch (HttpException $httpEx) {
                return $this->formatHttpException($httpEx, $request);
            }
            if ($ex instanceof HttpException) {
                return $this->formatHttpException($ex, $request);
            }
            return $this->formatUnhandledException($ex, $request);
        }
    }


    protected function matchRequest(Request $request): void
    {
        $matcher = $this->createUrlMatcher();
        $parameters = $matcher->matchRequest($request);
        $request->attributes->add($parameters);
        unset($parameters['_route'], $parameters['_controller']);
        $request->attributes->set('_route_params', $parameters);
    }


    protected function createUrlMatcher(): UrlMatcher
    {
        return new UrlMatcher($this->getRouteCollection(), $this->requestContext);
    }


    protected function getRouteCollection(): RouteCollection
    {
        if (!$this->routes) {
            $this->routes = new RouteCollection();
            $this->routeProvider->getRoutes($this->routes);
        }
        return $this->routes;
    }


    protected function getUrlGenerator(): UrlGeneratorInterface
    {
        if (!$this->urlGenerator) {
            $this->urlGenerator = new UrlGenerator($this->getRouteCollection(), $this->requestContext);
        }
        return $this->urlGenerator;
    }


    /**
     * Handles an exception that occurred while serving a request.
     *
     * The method may return NULL if it cannot handle the exception.
     * Or throw a HttpException.
     * Or return a Response.
     *
     * @param \Exception $exception
     * @param Request $request
     * @return null|Response
     */
    protected function handleException(\Exception $exception, Request $request): ?Response
    {
        if ($exception instanceof NoConfigurationException) {
            $msg = sprintf('Not found.');
            throw new HttpException(Response::HTTP_NOT_FOUND, $msg, $exception);
        }
        if ($exception instanceof MethodNotAllowedException) {
            $msg = sprintf('Method %s is not allowed.', $request->getMethod());
            throw new HttpException(Response::HTTP_METHOD_NOT_ALLOWED, $msg, $exception);
        }
        if ($exception instanceof ResourceNotFoundException) {
            $msg = sprintf('Not found.');
            throw new HttpException(Response::HTTP_NOT_FOUND, $msg, $exception);
        }
        return null;
    }


    /**
     * Creates a adequate Response for the given HttpException.
     *
     * @param HttpException $ex
     * @param Request $request
     * @return Response
     */
    protected function formatHttpException(HttpException $ex, Request $request): Response
    {
        $response = new Response();
        $response->setStatusCode($ex->getStatusCode());
        $response->setCharset('UTF-8');
        $response->setContent($ex->getMessage());
        $response->headers->replace($ex->getHeaders());
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }


    /**
     * Create a Response for the given unexpected exception.
     *
     * @param \Exception $ex
     * @param Request $request
     * @return Response
     */
    protected function formatUnhandledException(\Exception $ex, Request $request): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->setCharset('UTF-8');
        if ($this->includeExceptionDetails) {
            $response->setContent($ex->__toString());
        } else {
            $response->setContent('Internal Server Error');
        }
        return $response;
    }


}