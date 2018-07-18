<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 18.04.18
 * Time: 13:09
 */

namespace TS\Web\Microserver;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TS\Web\Microserver\Exception\HttpException;


class JsonServer extends Server
{

    /**
     * Creates a adequate Response for the given HttpException.
     *
     * @param HttpException $ex
     * @param Request $request
     * @return Response
     */
    protected function formatHttpException(HttpException $ex, Request $request): Response
    {
        $response = new JsonResponse();
        $response->headers->replace($ex->getHeaders());
        $response->setStatusCode($ex->getStatusCode());
        $response->setCharset('UTF-8');

        $data = [
            'message' => $ex->getMessage()
        ];
        if ($this->includeExceptionDetails) {
            $data['details'] = $ex->__toString();
        }
        $response->setData($data);

        return $response;
    }


    /**
     * Create a Response for the given unexpected exception.
     *
     * @param \Exception $ex
     * @param Request $request
     * @return Response
     */
    protected function formatUnhandledException(\Exception $ex, Request $request): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->setCharset('UTF-8');
        if ($this->includeExceptionDetails) {
            $response->setContent($ex->__toString());
        } else {
            $response->setContent('Internal Server Error');
        }
        return $response;
    }


}