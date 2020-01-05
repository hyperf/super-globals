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

namespace HyperfTest\SuperGlobals\Stub;

use Hyperf\Contract\ContainerInterface;
use Hyperf\HttpServer\Request;
use Hyperf\Utils\ApplicationContext;
use Mockery;

class ContainerStub
{
    public static function getContainer(): ContainerInterface
    {
        $container = Mockery::mock(ContainerInterface::class);
        ApplicationContext::setContainer($container);

        $container->shouldReceive('get')->with(Request::class)->andReturn(new Request());
        return $container;
    }
}
