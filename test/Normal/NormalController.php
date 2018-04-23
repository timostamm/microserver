<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 22:59
 */

namespace TS\Web\Microserver\Normal;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NormalController
{

    /**
     * @Route(path="/", methods={"GET"})
     */
    public function index()
    {
        return new Response("NormalController index");
    }


}