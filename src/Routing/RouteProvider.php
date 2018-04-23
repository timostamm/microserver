<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 18.04.18
 * Time: 13:43
 *
 * This file was copied and modified from the Symfony package.
 * License: MIT
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 */

namespace TS\Web\Microserver\Routing;


use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteProvider
{

    protected $reader;

    protected $routeAnnotationClass = 'Symfony\\Component\\Routing\\Annotation\\Route';

    protected $defaultRouteIndex = 0;

    protected $controllerClasses = [];


    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }


    public function addControllerClass(string $classname):void
    {
        if (!class_exists($classname)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $classname));
        }
        $this->controllerClasses[$classname] = null;
    }


    public function getRoutes(RouteCollection $routes): void
    {
        foreach ($this->controllerClasses as $classname => $instance) {
            $collection = $this->loadControllerRoutes($classname);
            $routes->addCollection($collection);
        }
    }


    protected function loadControllerRoutes(string $classname): RouteCollection
    {
        $class = new \ReflectionClass($classname);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class->getName()));
        }

        $globals = $this->getAnnotatedGlobals($class);

        $collection = new RouteCollection();

        foreach ($class->getMethods() as $method) {
            $this->defaultRouteIndex = 0;
            foreach ($this->reader->getMethodAnnotations($method) as $annot) {
                if ($annot instanceof $this->routeAnnotationClass) {
                    $this->addControllerRoute($collection, $annot, $globals, $class, $method);
                }
            }
        }

        if (0 === $collection->count() && $class->hasMethod('__invoke') && $annot = $this->reader->getClassAnnotation($class, $this->routeAnnotationClass)) {
            $globals['path'] = '';
            $globals['name'] = '';
            $this->addControllerRoute($collection, $annot, $globals, $class, $class->getMethod('__invoke'));
        }

        return $collection;
    }


    protected function addControllerRoute(RouteCollection $collection, $annot, $globals, \ReflectionClass $class, \ReflectionMethod $method) : void
    {
        $name = $annot->getName();
        if (null === $name) {
            $name = $this->getAnnotatedRouteDefaultName($class, $method);
        }
        $name = $globals['name'].$name;

        $defaults = array_replace($globals['defaults'], $annot->getDefaults());
        foreach ($method->getParameters() as $param) {
            if (false !== strpos($globals['path'].$annot->getPath(), sprintf('{%s}', $param->getName())) && !isset($defaults[$param->getName()]) && $param->isDefaultValueAvailable()) {
                $defaults[$param->getName()] = $param->getDefaultValue();
            }
        }
        $requirements = array_replace($globals['requirements'], $annot->getRequirements());
        $options = array_replace($globals['options'], $annot->getOptions());
        $schemes = array_merge($globals['schemes'], $annot->getSchemes());
        $methods = array_merge($globals['methods'], $annot->getMethods());

        $host = $annot->getHost();
        if (null === $host) {
            $host = $globals['host'];
        }

        $condition = $annot->getCondition();
        if (null === $condition) {
            $condition = $globals['condition'];
        }

        $route = $this->createRoute($globals['path'].$annot->getPath(), $defaults, $requirements, $options, $host, $schemes, $methods, $condition);

        if ('__invoke' === $method->getName()) {
            $route->setDefault('_controller', $class->getName());
        } else {
            $route->setDefault('_controller', $class->getName().'::'.$method->getName());
        }

        $collection->add($name, $route);
    }


    protected function getAnnotatedRouteDefaultName(\ReflectionClass $class, \ReflectionMethod $method) : string
    {
        $name = strtolower(str_replace('\\', '_', $class->name).'_'.$method->name);
        if ($this->defaultRouteIndex > 0) {
            $name .= '_'.$this->defaultRouteIndex;
        }
        ++$this->defaultRouteIndex;

        return $name;
    }

    protected function getAnnotatedGlobals(\ReflectionClass $class) : array
    {
        $globals = array(
            'path' => '',
            'requirements' => array(),
            'options' => array(),
            'defaults' => array(),
            'schemes' => array(),
            'methods' => array(),
            'host' => '',
            'condition' => '',
            'name' => '',
        );

        if ($annot = $this->reader->getClassAnnotation($class, $this->routeAnnotationClass)) {
            if (null !== $annot->getName()) {
                $globals['name'] = $annot->getName();
            }

            if (null !== $annot->getPath()) {
                $globals['path'] = $annot->getPath();
            }

            if (null !== $annot->getRequirements()) {
                $globals['requirements'] = $annot->getRequirements();
            }

            if (null !== $annot->getOptions()) {
                $globals['options'] = $annot->getOptions();
            }

            if (null !== $annot->getDefaults()) {
                $globals['defaults'] = $annot->getDefaults();
            }

            if (null !== $annot->getSchemes()) {
                $globals['schemes'] = $annot->getSchemes();
            }

            if (null !== $annot->getMethods()) {
                $globals['methods'] = $annot->getMethods();
            }

            if (null !== $annot->getHost()) {
                $globals['host'] = $annot->getHost();
            }

            if (null !== $annot->getCondition()) {
                $globals['condition'] = $annot->getCondition();
            }
        }

        return $globals;
    }


    protected function createRoute($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition)
    {
        return new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
    }




}