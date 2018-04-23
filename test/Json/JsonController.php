<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 22:59
 */

namespace TS\Web\Microserver\Json;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JsonController
{

    /**
     * @Route(path="/", methods={"GET"})
     */
    public function index()
    {
        return new Response("JsonController index");
    }


}