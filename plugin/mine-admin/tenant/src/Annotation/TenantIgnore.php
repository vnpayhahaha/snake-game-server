<?php

namespace Plugin\MineAdmin\Tenant\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class TenantIgnore extends AbstractAnnotation
{
    public function __construct(private readonly bool $onlyDefaultTenantVisible = false)
    {}

    public function getOnlyDefaultTenantVisible(): bool
    {
        return $this->onlyDefaultTenantVisible;
    }
}