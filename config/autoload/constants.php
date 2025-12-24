<?php

return [
    // 游戏群组配置
    'game_group_config' => \App\Constants\GameGroupConfig::class,
    // 游戏群组配置变更记录
    'game_group_config_log' => \App\Constants\GameGroupConfigLog::class,
    // 菜单状态
    'menu' => \App\Constants\Menu::class,
    // 玩家钱包绑定变更记录
    'player_wallet_binding_log' => \App\Constants\PlayerWalletBindingLog::class,
    // 奖励发放任务队列
    'prize_dispatch_queue' => \App\Constants\PrizeDispatchQueue::class,
    // 中奖记录
    'prize_record' => \App\Constants\PrizeRecord::class,
    // 奖金转账记录
    'prize_transfer' => \App\Constants\PrizeTransfer::class,
    // 蛇身节点
    'snake_node' => \App\Constants\SnakeNode::class,
    // Telegram命令消息记录
    'telegram_command_message_record' => \App\Constants\TelegramCommandMessageRecord::class,
    // TRON交易监听日志
    'tron_transaction_log' => \App\Constants\TronTransactionLog::class,
];