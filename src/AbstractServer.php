<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 18.04.18
 * Time: 13:09
 */

namespace TS\Web\Microserver;


use Exception;
use TS\Web\Microserver\Controller\ControllerInvokerInterface;
use TS\Web\Microserver\Controller\ControllerResolverInterface;
use TS\Web\Microserver\Routing\RouteProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;


abstract class AbstractServer
{

    protected $routeProvider;
    protected $controllerResolver;
    protected $controllerInvoker;
    private $routes = null;


    public function __construct(RouteProvider $routeProvider, ControllerResolverInterface $controllerResolver, ControllerInvokerInterface $controllerInvoker)
    {
        $this->routeProvider = $routeProvider;
        $this->controllerResolver = $controllerResolver;
        $this->controllerInvoker = $controllerInvoker;
    }


    public function serve(Request $request): Response
    {

        try {

            $this->matchRequest($request);

            $controller = $this->controllerResolver->getController($request);

            $response = new Response();

            $response = $this->preRequest($request, $response);

            $response = $this->controllerInvoker->invoke($controller, $request, $response);

            $response = $this->postRequest($request, $response);

            return $response;

        } catch (HttpException $ex) {
            return $this->handleHttpException($ex, $request);
        } catch (Exception $ex) {
            return $this->handleUncaughtException($ex, $request);
        }
    }


    protected function preRequest(Request $request, Response $response):Response
    {
        return $response;
    }


    protected function postRequest(Request $request, Response $response):Response
    {
        return $response;
    }


    protected function matchRequest(Request $request): void
    {
        $context = $this->createRequestContext($request);
        $matcher = $this->createUrlMatcher($context);

        try {

            $parameters = $matcher->matchRequest($request);
            $request->attributes->add($parameters);
            unset($parameters['_route'], $parameters['_controller']);
            $request->attributes->set('_route_params', $parameters);

        } catch (NoConfigurationException $exception) {
            $msg = sprintf('No routes configured.');
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $msg, $exception);

        } catch (MethodNotAllowedException $exception) {
            $msg = sprintf('Method %s is not allowed.', $request->getMethod());
            throw new HttpException(Response::HTTP_METHOD_NOT_ALLOWED, $msg, $exception);

        } catch (ResourceNotFoundException $exception) {
            $msg = sprintf('Not found.', $request->getMethod());
            throw new HttpException(Response::HTTP_NOT_FOUND, $msg, $exception);
        }
    }

    protected function createRequestContext(Request $request): RequestContext
    {
        $context = new RequestContext();
        $context->fromRequest($request);
        return $context;
    }

    protected function createUrlMatcher(RequestContext $context): UrlMatcher
    {
        return new UrlMatcher($this->getRouteCollection(), $context);
    }

    protected function getRouteCollection(): RouteCollection
    {
        if (!$this->routes) {
            $this->routes = new RouteCollection();
            $this->routeProvider->getRoutes($this->routes);
        }
        return $this->routes;
    }


    abstract protected function handleHttpException(HttpException $ex, Request $request): Response;


    abstract protected function handleUncaughtException(Exception $ex, Request $request): Response;


}