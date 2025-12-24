<?php

namespace Plugin\MineAdmin\Tenant\Middleware;

use Plugin\MineAdmin\Tenant\Model\Tenant;
use Plugin\MineAdmin\Tenant\Utils\TenantUtils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TenantDomainMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 去除协议、和最后一个 /
        $referer = str_replace(['http://', 'https://', '/'], ['', '', ''], $request->getHeaderLine('referer'));
        if (!empty($referer)) {
            if ($id = Tenant::query()->where('bind_domain', $referer)->value('id')) {
                // @phpstan-ignore-next-line
                $request->setHeader(TenantUtils::TENANT_ID, $id);
                return $handler->handle($request);
            }
        }
        return $handler->handle($request);
    }

}