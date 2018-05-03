<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 19.04.18
 * Time: 01:30
 */

namespace TS\Web\Microserver\Exception;

use Symfony\Component\HttpFoundation\Response;

class RedirectException extends HttpException
{

    public function __construct(string $location, int $statusCode = Response::HTTP_FOUND, string $message = null, \Exception $previous = null, array $headers = array(), ?int $code = 0)
    {
        $ok = [Response::HTTP_MOVED_PERMANENTLY, Response::HTTP_FOUND, Response::HTTP_SEE_OTHER, Response::HTTP_TEMPORARY_REDIRECT, Response::HTTP_PERMANENTLY_REDIRECT];
        if (! in_array($statusCode, $ok)) {
            $msg = sprintf('Invalid status code %s for RedirectException.', $statusCode);
            throw new \OutOfRangeException($msg);
        }
        $headers['Location'] = $location;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

}