<?php

declare(strict_types=1);

namespace App\Crontab;

use App\Model\Game\GameGroupConfig;
use App\Service\Tron\TronService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use Throwable;

#[Crontab(name: "TronMonitorCrontab", rule: "*/3 * * * * *", singleton: true, onOneServer: true)]
class TronMonitorCrontab
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    #[Inject]
    private TronService $tronService;

    public function execute()
    {
        $this->logger->info(sprintf('TronMonitorCrontab execute at %s', date('Y-m-d H:i:s')));

        // 获取所有活跃的游戏群组配置
        $activeGroupConfigs = GameGroupConfig::where('status', 1)->get();

        /** @var GameGroupConfig $config */
        foreach ($activeGroupConfigs as $config) {
            try {
                // 如果钱包正在变更中，则跳过本次监听
                if ($config->wallet_change_status === GameGroupConfig::WALLET_CHANGE_STATUS_CHANGING) {
                    $this->logger->info(sprintf('Group %d wallet is changing, skip monitoring.', $config->id));
                    continue;
                }

                // 获取上次处理的区块高度
                $lastProcessedBlock = $this->tronService->getLastProcessedBlock($config->id);

                // 查询新交易并处理
                $this->tronService->monitorNewTransactions($config, $lastProcessedBlock);

            } catch (Throwable $e) {
                $this->logger->error(sprintf('TronMonitorCrontab error for group %d: %s', $config->id, $e->getMessage()));
                // 异常处理：记录日志，告警等
            }
        }
    }
}
