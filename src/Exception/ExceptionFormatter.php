<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 22:22
 */

namespace TS\Web\Microserver\Exception;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class ExceptionFormatter
{

    /**
     * Creates a adequate Response for the given HttpException.
     *
     * @param HttpException $ex
     * @param Request $request
     * @return Response
     */
    abstract public function formatHttpException(HttpException $ex, Request $request): Response;


    /**
     * Create a Response for the given unexpected exception.
     *
     * @param \Exception $ex
     * @param Request $request
     * @return Response
     */
    abstract public function formatUnhandledException(\Exception $ex, Request $request): Response;


}