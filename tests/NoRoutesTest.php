<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 13.09.18
 * Time: 16:33
 */

namespace TS\Web\Microserver;


use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class NoRoutesTest extends TestCase
{

    public function testNotConfigured()
    {
        $server = new Server();
        $request = Request::create('http://localhost:8080/', 'GET', [], [], [], [], '');
        $response = $server->serve($request);
        $this->assertEquals(404, $response->getStatusCode());

    }

}