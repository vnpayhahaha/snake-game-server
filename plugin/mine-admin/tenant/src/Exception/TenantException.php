<?php

namespace Plugin\MineAdmin\Tenant\Exception;

use Plugin\MineAdmin\Tenant\Utils\Result;
use Plugin\MineAdmin\Tenant\Utils\ResultCode;

class TenantException extends \RuntimeException
{
    private Result $response;

    public function __construct(ResultCode $code = ResultCode::FAIL, ?string $message = null, mixed $data = [])
    {
        $this->response = new Result($code, $message, $data);
    }

    public function getResponse(): Result
    {
        return $this->response;
    }
}