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

namespace Hyperf\SuperGlobals\Proxy;

use Hyperf\Contract\SessionInterface;
use Hyperf\SuperGlobals\Exception\InvalidOperationException;
use Hyperf\SuperGlobals\Exception\SessionNotFoundException;
use Hyperf\SuperGlobals\Proxy;
use Psr\Http\Message\ServerRequestInterface;

class Session extends Proxy
{
    public function toArray(): array
    {
        return $this->getSession()->all();
    }

    public function offsetExists($offset)
    {
        return $this->getSession()->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getSession()->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->getSession()->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->getSession()->remove($offset);
    }

    protected function getSession(): SessionInterface
    {
        $session = $this->getContainer()->get(SessionInterface::class);
        if (! $session instanceof SessionInterface) {
            throw new SessionNotFoundException();
        }

        return $session;
    }

    protected function override(ServerRequestInterface $request, array $data): ServerRequestInterface
    {
        throw new InvalidOperationException();
    }
}
