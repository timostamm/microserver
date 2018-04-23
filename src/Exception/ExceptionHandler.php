<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 22:22
 */

namespace TS\Web\Microserver\Exception;

use TS\Web\Microserver\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class ExceptionHandler
{

    abstract public function handleHttpException(HttpException $ex, Request $request): Response;

    abstract public function handleUncaughtException(\Exception $ex, Request $request): Response;


}