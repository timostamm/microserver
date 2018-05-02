<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 22:22
 */

namespace TS\Web\Microserver\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonExceptionFormatter extends ExceptionFormatter
{

    protected $includeDetails;

    public function __construct(bool $includeDetails = false)
    {
        $this->includeDetails = $includeDetails;
    }


    public function formatHttpException(HttpException $ex, Request $request): Response
    {
        $response = new JsonResponse();
        $response->headers->replace($ex->getHeaders());
        $response->setStatusCode($ex->getStatusCode());
        $response->setCharset('UTF-8');

        $data = [
            'message' => $ex->getMessage()
        ];
        if ($this->includeDetails) {
            $data['details'] = $ex->__toString();
        }
        $response->setData($data);

        return $response;
    }


    public function formatUnhandledException(\Exception $ex, Request $request): Response
    {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->setCharset('UTF-8');
        if ($this->includeDetails) {
            $response->setData([
                'message' => $ex->getMessage(),
                'details' => $ex->__toString()
            ]);
        } else {
            $response->setData([
                'message' => 'Internal Server Error'
            ]);
        }
        return $response;
    }

}