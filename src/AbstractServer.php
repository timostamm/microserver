<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 18.04.18
 * Time: 13:09
 */

namespace TS\Web\Microserver;


use Exception;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TS\Web\Microserver\Controller\ControllerInvokerInterface;
use TS\Web\Microserver\Controller\ControllerResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use TS\Web\Microserver\Routing\RouteProviderInterface;


abstract class AbstractServer
{

    protected $routeProvider;
    protected $controllerResolver;
    protected $controllerInvoker;
    protected $requestContext;
    protected $urlGenerator;
    private $routes = null;


    public function __construct(RouteProviderInterface $routeProvider, ControllerResolverInterface $controllerResolver, ControllerInvokerInterface $controllerInvoker)
    {
        $this->routeProvider = $routeProvider;
        $this->controllerResolver = $controllerResolver;
        $this->controllerInvoker = $controllerInvoker;
        $this->requestContext = new RequestContext();
    }


    public function serve(Request $request): Response
    {

        try {

            $this->requestContext->fromRequest($request);

            $this->matchRequest($request);

            $controller = $this->controllerResolver->getController($request);

            $response = new Response();

            $response = $this->preRequest($request, $response);

            $response = $this->controllerInvoker->invoke($controller, $request, $response);

            $response = $this->postRequest($request, $response);

            return $response;

        } catch (Exception $ex) {
            return $this->handleException($ex, $request);
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


    protected function getUrlGenerator():UrlGeneratorInterface
    {
        if (!$this->urlGenerator) {
            $this->urlGenerator = new UrlGenerator($this->getRouteCollection(), $this->requestContext);
        }
        return $this->urlGenerator;
    }


    abstract protected function handleException(Exception $ex, Request $request): Response;


}