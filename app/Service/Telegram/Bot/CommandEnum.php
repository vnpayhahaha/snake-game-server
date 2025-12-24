<?php

namespace App\Service\Telegram\Bot;

/**
 * Snake Chain Game - Telegram Bot 指令枚举
 */
class CommandEnum
{
    // Redis 队列名称
    public const string TELEGRAM_COMMAND_RUN_QUEUE_NAME = 'telegram-command-run-queue';
    public const string TELEGRAM_NOTICE_QUEUE_NAME = 'telegram-notice-queue';
    public const string TRON_TX_PROCESS_QUEUE_NAME = 'tron-tx-process-queue';
    public const string PRIZE_DISPATCH_QUEUE_NAME = 'prize-dispatch-queue';

    // 英文指令集
    public const array COMMAND_SET = [
        // 基础指令
        'start'         => 'Start',
        'help'          => 'Help',

        // 用户钱包指令
        'bindwallet'    => 'BindWallet',
        'unbindwallet'  => 'UnbindWallet',
        'mywallet'      => 'MyWallet',

        // 游戏查询指令
        'snake'         => 'Snake',
        'mytickets'     => 'MyTickets',
        'ticket'        => 'Ticket',
        'myprizes'      => 'MyPrizes',
        'history'       => 'History',
        'stats'         => 'Stats',
        'rules'         => 'Rules',
        'address'       => 'Address',

        // 管理员指令
        'bind'          => 'BindTenant',
        'wallet'        => 'SetWallet',
        'cancelwallet'  => 'CancelWallet',
        'setbet'        => 'SetBet',
        'setfee'        => 'SetFee',
        'info'          => 'Info',
    ];

    // 英文指令说明
    public static array $commandDescMap = [
        // 基础指令
        'start'        => "<blockquote>[示例] /start" . PHP_EOL . "启动机器人，显示欢迎信息</blockquote>",
        'help'         => "<blockquote>[示例] /help" . PHP_EOL . "显示所有可用指令</blockquote>",

        // 用户钱包指令
        'bindwallet'   => "<blockquote>[示例] /bindwallet TYourWalletAddress123456789" . PHP_EOL . "[参数] wallet_address !TRON钱包地址" . PHP_EOL . "绑定您的TRON钱包到当前群组</blockquote>",
        'unbindwallet' => "<blockquote>[示例] /unbindwallet" . PHP_EOL . "解绑当前群组的钱包绑定</blockquote>",
        'mywallet'     => "<blockquote>[示例] /mywallet" . PHP_EOL . "查看您在当前群组绑定的钱包信息</blockquote>",

        // 游戏查询指令
        'snake'        => "<blockquote>[示例] /snake" . PHP_EOL . "查看当前群组的蛇身状态（长度、最新节点等）</blockquote>",
        'mytickets'    => "<blockquote>[示例] /mytickets" . PHP_EOL . "查看您在当前群组的购彩记录（最近10条）</blockquote>",
        'ticket'       => "<blockquote>[示例] /ticket 20250108-001" . PHP_EOL . "[参数] serial_no !购彩流水号" . PHP_EOL . "查询指定流水号的购彩记录详情</blockquote>",
        'myprizes'     => "<blockquote>[示例] /myprizes" . PHP_EOL . "查看您在当前群组的中奖记录（最近10条）</blockquote>",
        'history'      => "<blockquote>[示例] /history" . PHP_EOL . "查看当前群组历史中奖记录（最近10条）</blockquote>",
        'stats'        => "<blockquote>[示例] /stats" . PHP_EOL . "查看当前群组游戏统计数据</blockquote>",
        'rules'        => "<blockquote>[示例] /rules" . PHP_EOL . "查看详细游戏规则说明</blockquote>",
        'address'      => "<blockquote>[示例] /address" . PHP_EOL . "查看当前群组收款钱包地址</blockquote>",

        // 管理员指令
        'bind'         => "<blockquote>[示例] /bind 000001" . PHP_EOL . "[参数] tenant_id !租户编号" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "绑定群组到租户</blockquote>",
        'wallet'       => "<blockquote>[示例] /wallet TNewWalletAddress123456789" . PHP_EOL . "[参数] wallet_address !新的TRON钱包地址" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "设置/更新钱包地址（10分钟冷却期）</blockquote>",
        'cancelwallet' => "<blockquote>[示例] /cancelwallet" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "取消钱包地址变更</blockquote>",
        'setbet'       => "<blockquote>[示例] /setbet 5" . PHP_EOL . "[参数] amount !投注金额(TRX)" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "设置投注金额</blockquote>",
        'setfee'       => "<blockquote>[示例] /setfee 10" . PHP_EOL . "[参数] rate !手续费比例(%)" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "设置平台手续费比例</blockquote>",
        'info'         => "<blockquote>[示例] /info" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "查看当前群组完整配置信息</blockquote>",
    ];

    // 中文指令集
    public const COMMAND_SET_CN = [
        // 基础指令
        '开始'         => 'cnStart',
        '帮助'         => 'cnHelp',

        // 用户钱包指令
        '绑定钱包'     => 'cnBindWallet',
        '解绑钱包'     => 'cnUnbindWallet',
        '我的钱包'     => 'cnMyWallet',

        // 游戏查询指令
        '蛇身'         => 'cnSnake',
        '我的购彩'     => 'cnMyTickets',
        '查询票号'     => 'cnTicket',
        '我的中奖'     => 'cnMyPrizes',
        '历史中奖'     => 'cnHistory',
        '游戏统计'     => 'cnStats',
        '游戏规则'     => 'cnRules',
        '收款地址'     => 'cnAddress',

        // 管理员指令
        '绑定租户'     => 'cnBindTenant',
        '设置钱包'     => 'cnSetWallet',
        '取消钱包变更' => 'cnCancelWallet',
        '设置投注'     => 'cnSetBet',
        '设置手续费'   => 'cnSetFee',
        '群组配置'     => 'cnInfo',
    ];

    // 中文指令说明
    public static array $commandDescCnMap = [
        // 基础指令
        '开始'         => "<blockquote>[示例] /开始" . PHP_EOL . "启动机器人，显示欢迎信息</blockquote>",
        '帮助'         => "<blockquote>[示例] /帮助" . PHP_EOL . "显示所有可用指令</blockquote>",

        // 用户钱包指令
        '绑定钱包'     => "<blockquote>[示例] /绑定钱包 TYourWalletAddress123456789" . PHP_EOL . "[参数] wallet_address !TRON钱包地址" . PHP_EOL . "绑定您的TRON钱包到当前群组</blockquote>",
        '解绑钱包'     => "<blockquote>[示例] /解绑钱包" . PHP_EOL . "解绑当前群组的钱包绑定</blockquote>",
        '我的钱包'     => "<blockquote>[示例] /我的钱包" . PHP_EOL . "查看您在当前群组绑定的钱包信息</blockquote>",

        // 游戏查询指令
        '蛇身'         => "<blockquote>[示例] /蛇身" . PHP_EOL . "查看当前群组的蛇身状态（长度、最新节点等）</blockquote>",
        '我的购彩'     => "<blockquote>[示例] /我的购彩" . PHP_EOL . "查看您在当前群组的购彩记录（最近10条）</blockquote>",
        '查询票号'     => "<blockquote>[示例] /查询票号 20250108-001" . PHP_EOL . "[参数] serial_no !购彩流水号" . PHP_EOL . "查询指定流水号的购彩记录详情</blockquote>",
        '我的中奖'     => "<blockquote>[示例] /我的中奖" . PHP_EOL . "查看您在当前群组的中奖记录（最近10条）</blockquote>",
        '历史中奖'     => "<blockquote>[示例] /历史中奖" . PHP_EOL . "查看当前群组历史中奖记录（最近10条）</blockquote>",
        '游戏统计'     => "<blockquote>[示例] /游戏统计" . PHP_EOL . "查看当前群组游戏统计数据</blockquote>",
        '游戏规则'     => "<blockquote>[示例] /游戏规则" . PHP_EOL . "查看详细游戏规则说明</blockquote>",
        '收款地址'     => "<blockquote>[示例] /收款地址" . PHP_EOL . "查看当前群组收款钱包地址</blockquote>",

        // 管理员指令
        '绑定租户'     => "<blockquote>[示例] /绑定租户 000001" . PHP_EOL . "[参数] tenant_id !租户编号" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "绑定群组到租户</blockquote>",
        '设置钱包'     => "<blockquote>[示例] /设置钱包 TNewWalletAddress123456789" . PHP_EOL . "[参数] wallet_address !新的TRON钱包地址" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "设置/更新钱包地址（10分钟冷却期）</blockquote>",
        '取消钱包变更' => "<blockquote>[示例] /取消钱包变更" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "取消钱包地址变更</blockquote>",
        '设置投注'     => "<blockquote>[示例] /设置投注 5" . PHP_EOL . "[参数] amount !投注金额(TRX)" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "设置投注金额</blockquote>",
        '设置手续费'   => "<blockquote>[示例] /设置手续费 10" . PHP_EOL . "[参数] rate !手续费比例(%)" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "设置平台手续费比例</blockquote>",
        '群组配置'     => "<blockquote>[示例] /群组配置" . PHP_EOL . "[权限] 群管理员" . PHP_EOL . "查看当前群组完整配置信息</blockquote>",
    ];

    /**
     * 判断是否是有效指令
     */
    public static function isCommand(string $command): bool
    {
        $command_set_cn_keys = array_keys(self::COMMAND_SET_CN);
        $command_set_keys = array_keys(self::COMMAND_SET);
        return in_array($command, $command_set_cn_keys, true)
            || in_array(strtolower(trim($command)), $command_set_keys, true);
    }

    /**
     * 获取指令对应的方法名
     */
    public static function getCommand(string $command): string
    {
        $command_set_cn_keys = array_keys(self::COMMAND_SET_CN);
        if (in_array($command, $command_set_cn_keys, true)) {
            return self::COMMAND_SET_CN[$command];
        }
        $command_set_keys = array_keys(self::COMMAND_SET);
        if (in_array(strtolower($command), $command_set_keys, true)) {
            return self::COMMAND_SET[strtolower($command)];
        }
        return '';
    }

    /**
     * 获取帮助信息
     */
    public static function getHelpReply(bool $isCn = false): array
    {
        $reply = [];
        if ($isCn) {
            $reply[] = '***** Snake Chain Game 指令列表 *****';
            $reply[] = '';
            $keys = array_keys(self::COMMAND_SET_CN);
            foreach ($keys as $key) {
                $reply[] = '/' . $key;
                $reply[] = self::$commandDescCnMap[$key];
                $reply[] = '';
            }
            $reply[] = '------------------------';
            $reply[] = '💡 提示：发送TRON到收款地址即可自动购彩';
            $reply[] = '🎮 游戏规则：票号连续或区间匹配即可中奖';
        } else {
            $reply[] = '***** Snake Chain Game Command List *****';
            $reply[] = '';
            $keys = array_keys(self::COMMAND_SET);
            foreach ($keys as $key) {
                $reply[] = '/' . $key;
                $reply[] = self::$commandDescMap[$key];
                $reply[] = '';
            }
            $reply[] = '------------------------';
            $reply[] = '💡 Tip: Send TRON to the payment address to play automatically';
            $reply[] = '🎮 Rules: Win when ticket numbers are consecutive or match intervals';
        }
        return $reply;
    }
}
