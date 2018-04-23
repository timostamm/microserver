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

class SimpleExceptionHandler extends ExceptionHandler
{

    protected $includeDetails;

    public function __construct(bool $includeDetails = false)
    {
        $this->includeDetails = $includeDetails;
    }


    public function handleHttpException(HttpException $ex, Request $request): Response
    {
        $response = new Response();
        $response->setStatusCode($ex->getStatusCode());
        $response->setCharset('UTF-8');
        $response->setContent($ex->getMessage());
        $response->headers->replace($ex->getHeaders());
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }


    public function handleUncaughtException(\Exception $ex, Request $request): Response
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->setCharset('UTF-8');
        if ($this->includeDetails) {
            $response->setContent($ex->__toString());
        } else {
            $response->setContent('Internal Server Error');
        }
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }


}