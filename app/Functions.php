<?php

if (!function_exists('t')) {

    function t(string $key, array $replace = []): string
    {
        $locale = \Hyperf\Context\Context::get('locale', 'zh_CN');
        return trans($key, $replace, $locale);
    }
}

// 双语返回 tt
if (!function_exists('tt')) {
    function tt(string $key, array $replace = []): array
    {
        return [
            'zh' => trans($key, $replace, 'zh_CN'),
            'en' => trans($key, $replace, 'en_US'),
        ];
    }
}