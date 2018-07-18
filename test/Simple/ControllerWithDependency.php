<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 22:59
 */

namespace TS\Web\Microserver\Simple;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ControllerWithDependency
{

    private $dependency;

    public function __construct(string $dependency)
    {
        $this->dependency = $dependency;
    }


    /**
     * @Route(path="/dependency", methods={"GET"})
     */
    public function index()
    {
        return new Response("ControllerWithDependency: " . $this->dependency);
    }


}