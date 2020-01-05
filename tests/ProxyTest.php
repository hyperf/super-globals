<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace HyperfTest\SuperGlobals;

use Hyperf\Contract\SessionInterface;
use Hyperf\SuperGlobals\Proxy\Cookie;
use Hyperf\SuperGlobals\Proxy\File;
use Hyperf\SuperGlobals\Proxy\Get;
use Hyperf\SuperGlobals\Proxy\Post;
use Hyperf\SuperGlobals\Proxy\Request;
use Hyperf\SuperGlobals\Proxy\Server;
use Hyperf\SuperGlobals\Proxy\Session;
use Hyperf\Utils\Context;
use HyperfTest\SuperGlobals\Stub\ContainerStub;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @internal
 * @coversNothing
 */
class ProxyTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        Context::set(ServerRequestInterface::class, null);
        Context::set('http.request.parsedData', null);
    }

    public function testCookie()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getCookieParams')->andReturn([
            'id' => $id = uniqid(),
        ]);
        Context::set(ServerRequestInterface::class, $request);
        $proxy = new Cookie();

        $this->assertSame($id, $proxy['id']);
    }

    public function testFile()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getUploadedFiles')->andReturn([
            'file' => $file = Mockery::mock(UploadedFileInterface::class),
        ]);
        Context::set(ServerRequestInterface::class, $request);
        $proxy = new File();

        $this->assertSame($file, $proxy['file']);
    }

    public function testGet()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getQueryParams')->andReturn([
            'id' => $id = uniqid(),
        ]);
        Context::set(ServerRequestInterface::class, $request);
        $proxy = new Get();

        $this->assertSame($id, $proxy['id']);
    }

    public function testPost()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')->andReturn([
            'id' => $id = uniqid(),
        ]);
        Context::set(ServerRequestInterface::class, $request);
        $proxy = new Post();

        $this->assertSame($id, $proxy['id']);
    }

    public function testRequest()
    {
        ContainerStub::getContainer();

        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getQueryParams')->andReturn([
            'id' => $id = '0' . uniqid(),
        ]);
        $request->shouldReceive('getParsedBody')->andReturn([
            'name' => $name = 'Hyperf' . uniqid(),
        ]);
        Context::set(ServerRequestInterface::class, $request);
        $proxy = new Request();

        $this->assertSame($id, $proxy['id']);
        $this->assertSame($name, $proxy['name']);
    }

    public function testServer()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')->andReturn([
            'server_name' => $name = 'Server.' . uniqid(),
        ]);
        $request->shouldReceive('getHeaders')->andReturn([
            'X-Token' => $token = uniqid(),
        ]);
        Context::set(ServerRequestInterface::class, $request);
        $proxy = new Server([]);

        $this->assertSame($name, $proxy['server_name']);
        $this->assertSame($token, $proxy['HTTP_X_TOKEN']);
    }

    public function testSession()
    {
        $container = ContainerStub::getContainer();
        $id = uniqid();
        $container->shouldReceive('get')->with(SessionInterface::class)->andReturnUsing(function () use ($id) {
            $session = Mockery::mock(SessionInterface::class);
            $session->shouldReceive('get')->with('id')->andReturn($id);
            return $session;
        });

        $proxy = new Session();
        $this->assertSame($id, $proxy['id']);
    }
}
