<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 19.04.18
 * Time: 01:30
 */

namespace TS\Web\Microserver;

use RuntimeException;

class HttpException extends RuntimeException
{

    private $statusCode;
    private $headers;

    public function __construct(int $statusCode, string $message = null, \Exception $previous = null, array $headers = array(), ?int $code = 0)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function getStatusCode():int
    {
        return $this->statusCode;
    }

    public function getHeaders():array
    {
        return $this->headers;
    }


}