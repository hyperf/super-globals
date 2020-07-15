<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\SuperGlobals\Proxy;

use Hyperf\SuperGlobals\Exception\InvalidOperationException;
use Hyperf\SuperGlobals\Proxy;
use Hyperf\Utils\Str;
use Psr\Http\Message\ServerRequestInterface;

class Server extends Proxy
{
    /**
     * @var array
     */
    protected $default;

    public function __construct(array $default)
    {
        $this->default = $default;
    }

    public function toArray(): array
    {
        $headers = [];
        foreach ($this->getRequest()->getHeaders() as $key => $value) {
            $headers['HTTP_' . str_replace('-', '_', Str::upper($key))] = $value;
        }
        $result = [];
        foreach (array_merge($this->default, $this->getRequest()->getServerParams(), $headers) as $key => $value) {
            $key = Str::upper($key);
            if (is_array($value) && count($value) == 1) {
                $result[$key] = $value[0];
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    protected function override(ServerRequestInterface $request, array $data): ServerRequestInterface
    {
        throw new InvalidOperationException('Invalid operation for $_SERVER.');
    }
}
