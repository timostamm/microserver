<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 13.09.18
 * Time: 16:33
 */

namespace TS\Web\Microserver;


use dummy\Simple\SimpleController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class BasicTest extends TestCase
{

    /** @var Server */
    protected $server;

    protected function setUp()
    {
        $this->server = new Server(true);
        $this->server->addController(SimpleController::class);
    }

    public function testIndex()
    {
        $request = Request::create('http://localhost:8080/', 'GET', [], [], [], [], '');
        $response = $this->server->serve($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('SimpleController index', $response->getContent());

    }

    public function testHello()
    {
        $request = Request::create('http://localhost:8080/hello', 'GET', [], [], [], [], '');
        $response = $this->server->serve($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('SimpleController hello', $response->getContent());

    }

}