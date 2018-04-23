<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 22:59
 */

namespace TS\Web\Microserver\Simple;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SimpleController
{

    /**
     * @Route(path="/", methods={"POST", "GET"})
     */
    public function index()
    {
        return new Response("SimpleController index");
    }


    /**
     * @Route(path="/hello", methods={"POST", "GET"})
     */
    public function hello()
    {
        return new Response("SimpleController hello ");
    }


    /**
     * @Route(path="/bar/{int}", methods={"GET"} )
     */
    public function bar( Request $request, Response $response, int $int)
    {
        return new Response("SimpleController bar " . $int );
    }


}