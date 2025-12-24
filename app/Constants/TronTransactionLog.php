<?php

namespace App\Constants;

use App\Constants\Lib\ConstantsOptionTrait;

/**
 * TRON交易监听日志常量
 */
class TronTransactionLog
{
    use ConstantsOptionTrait;

    // 交易类型 (1=入账 2=出账)
    public const TRANSACTION_TYPE_INCOME = 1;
    public const TRANSACTION_TYPE_EXPENSE = 2;
    public static array $transaction_type_list = [
        self::TRANSACTION_TYPE_INCOME => 'tron_transaction_log.enums.transaction_type.1',
        self::TRANSACTION_TYPE_EXPENSE => 'tron_transaction_log.enums.transaction_type.2',
    ];

    // 是否有效交易 (0=否 1=是)
    public const IS_VALID_NO = 0;
    public const IS_VALID_YES = 1;
    public static array $is_valid_list = [
        self::IS_VALID_NO => 'tron_transaction_log.enums.is_valid.0',
        self::IS_VALID_YES => 'tron_transaction_log.enums.is_valid.1',
    ];

    // 是否已处理 (0=否 1=是)
    public const PROCESSED_NO = 0;
    public const PROCESSED_YES = 1;
    public static array $processed_list = [
        self::PROCESSED_NO => 'tron_transaction_log.enums.processed.0',
        self::PROCESSED_YES => 'tron_transaction_log.enums.processed.1',
    ];
}
