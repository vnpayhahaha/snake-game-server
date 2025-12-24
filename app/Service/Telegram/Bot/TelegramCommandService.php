<?php

namespace App\Service\Telegram\Bot;

use App\Kernel\Wallet\Tron;
use App\Model\Game\GameGroup;
use App\Model\Game\GameGroupConfig;
use App\Model\Player\PlayerWalletBinding;
use App\Repository\Game\GameGroupConfigRepository;
use App\Repository\Game\GameGroupRepository;
use App\Repository\Player\PlayerWalletBindingRepository;
use App\Repository\Prize\PrizeRecordRepository;
use App\Repository\Snake\SnakeNodeRepository;
use App\Repository\Telegram\TelegramCommandMessageRecordRepository;
use App\Service\Player\PlayerWalletBindingService;
use App\Service\Prize\PrizeService;
use App\Service\Snake\SnakeService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Telegram as TelegramBot;

/**
 * Snake Chain Game - Telegram 指令服务
 */
class TelegramCommandService
{
    public $telegramBot;

    #[Inject]
    protected GameGroupConfigRepository $gameGroupConfigRepository;

    #[Inject]
    protected GameGroupRepository $gameGroupRepository;

    #[Inject]
    protected PlayerWalletBindingRepository $playerWalletBindingRepository;

    #[Inject]
    protected TelegramCommandMessageRecordRepository $telegramCommandMessageRecordRepository;

    #[Inject]
    protected SnakeNodeRepository $snakeNodeRepository;

    #[Inject]
    protected PrizeRecordRepository $prizeRecordRepository;

    #[Inject]
    protected PlayerWalletBindingService $playerWalletBindingService;

    #[Inject]
    protected SnakeService $snakeService;

    #[Inject]
    protected PrizeService $prizeService;

    #[Inject]
    protected StdoutLoggerInterface $logger;

    public function __construct()
    {
    }

    public function setTelegramBot($telegramBot): void
    {
        $this->telegramBot = $telegramBot;
    }

    private function getChatId(): int
    {
        return (int)$this->telegramBot->ChatID();
    }


    private function getUserId(): int
    {
        return (int)$this->telegramBot->UserId();
    }


    /**
     * 获取当前群组信息
     */
    private function getGroup(): ?GameGroup
    {
        $chatID = $this->telegramBot->ChatID();
        return $this->gameGroupRepository->getByTgChatId($chatID);
    }

    /**
     * 获取玩家钱包绑定信息
     */
    private function getPlayerWalletBinding(int $groupId, int $tgUserId): ?PlayerWalletBinding
    {
        return $this->playerWalletBindingRepository->getByGroupAndUser($groupId, $tgUserId);
    }

    /**
     * 检查是否为群组管理员
     */
    private function isGroupAdmin(): bool
    {
        try {
            $chatId = $this->telegramBot->ChatID();
            $userId = $this->telegramBot->UserId();

            // 使用 Telegram Bot API 获取用户在群组中的信息
            $member = $this->telegramBot->getChatMember([
                'chat_id' => $chatId,
                'user_id' => $userId
            ]);

            if (isset($member['result']['status'])) {
                $status = $member['result']['status'];
                // 创建者或管理员
                return in_array($status, ['creator', 'administrator'], true);
            }

            return false;
        } catch (\Exception $e) {
            logger()->error('检查管理员权限失败', [
                'error' => $e->getMessage(),
                'chat_id' => $this->telegramBot->ChatID(),
                'user_id' => $this->telegramBot->UserId()
            ]);
            return false;
        }
    }

    /**
     * 管理员权限验证失败响应
     */
    private function adminPermissionDenied(): array
    {
        return [
            '⛔️ Permission Denied',
            'This command requires administrator privileges.',
        ];
    }

    /**
     * 管理员权限验证失败响应（中文）
     */
    private function adminPermissionDeniedCn(): array
    {
        return [
            '⛔️ 权限不足',
            '此指令需要群组管理员权限。',
        ];
    }

    /**
     * 将英文结果翻译为中文
     */
    private function translateToChineseResult(array $result, string $commandType): array
    {
        // 根据不同的指令类型进行翻译
        switch ($commandType) {
            case 'snake':
                // 对于蛇身状态，直接调用中文版本的服务方法
                return $this->snakeService->getCurrentSnakeInfoCn($this->getChatId());
            case 'bindwallet':
            case 'unbindwallet':
            case 'mywallet':
                // 钱包相关指令的翻译
                return $this->translateWalletResult($result);
            default:
                return $result;
        }
    }

    /**
     * 将结果翻译为英文
     */
    private function translateToEnglishResult(array $result, string $commandType): array
    {
        // 对于英文指令，确保返回英文消息
        return $result;
    }

    /**
     * 翻译钱包相关结果
     */
    private function translateWalletResult(array $result): array
    {
        $translations = [
            'Invalid TRON wallet address format' => '无效的TRON钱包地址格式',
            'This wallet address is already bound to your account' => '此钱包地址已绑定到您的账户',
            'Wallet address updated successfully!' => '钱包地址更新成功！',
            'New address:' => '新地址：',
            'This wallet address is already bound to another user' => '此钱包地址已被其他用户绑定',
            'Wallet address bound successfully!' => '钱包地址绑定成功！',
            'Address:' => '地址：',
            'You can now participate in the game by sending TRX to the group payment address' => '您现在可以通过向群组收款地址发送TRX来参与游戏',
            'Failed to bind wallet address, please try again later' => '绑定钱包地址失败，请稍后重试',
            'No wallet address bound to your account in this group' => '您在此群组中没有绑定钱包地址',
            'Wallet address unbound successfully!' => '钱包地址解绑成功！',
            'Unbound address:' => '解绑地址：',
            'Failed to unbind wallet address, please try again later' => '解绑钱包地址失败，请稍后重试',
            'No wallet address bound to your account' => '您的账户没有绑定钱包地址',
            'Use /bindwallet <address> to bind your TRON wallet' => '使用 /绑定钱包 <地址> 绑定您的TRON钱包',
            'Your Wallet Information:' => '您的钱包信息：',
            'Bound at:' => '绑定时间：',
            'You can use /unbindwallet to unbind this address' => '您可以使用 /解绑钱包 解绑此地址',
            'Failed to query wallet information, please try again later' => '查询钱包信息失败，请稍后重试',
        ];

        $translatedResult = [];
        foreach ($result as $line) {
            $translatedLine = $line;
            foreach ($translations as $english => $chinese) {
                if (str_contains($line, $english)) {
                    $translatedLine = str_replace($english, $chinese, $line);
                    break;
                }
            }
            $translatedResult[] = $translatedLine;
        }

        return $translatedResult;
    }

    // --- 基础指令 ---

    // ==================== 英文指令 ====================

    /**
     * /start - 启动机器人
     */
    public function Start(int $userId, array $params, int $recordID): string|array
    {
        return [
            '🐍 Welcome to Snake Chain Game!',
            '',
            '**What is Snake Chain Game?**',
            'A blockchain-based lottery game on TRON.',
            'Each purchase creates a "snake node" with a unique ticket number.',
            'Win prizes when ticket numbers are consecutive or match intervals!',
            '',
            '**How to play:**',
            '1. Bind your TRON wallet: /bindwallet [your_wallet_address]',
            '2. Send TRON to the group payment address',
            '3. Watch the snake grow and check for wins!',
            '',
            '**Need help?**',
            'Use /help to see all commands',
            '',
            '🎮 Good luck and have fun!',
        ];
    }

    /**
     * /help - 显示帮助
     */
    public function Help(int $userId, array $params, int $recordID): string|array
    {
        return CommandEnum::getHelpReply(false);
    }

    public function cnStart(int $userId, array $params, int $recordID): string|array
    {
        return [
            '🐍 欢迎来到贪吃蛇链上游戏！',
            '',
            '**什么是贪吃蛇链上游戏？**',
            '基于TRON区块链的彩票游戏。',
            '每次购买都会创建一个带有唯一票号的"蛇身节点"。',
            '当票号连续或匹配区间时即可中奖！',
            '',
            '**如何游戏：**',
            '1. 绑定您的TRON钱包：/绑定钱包 [您的钱包地址]',
            '2. 向群组收款地址发送TRX',
            '3. 观看蛇身增长并检查中奖情况！',
            '',
            '**需要帮助？**',
            '使用 /帮助 查看所有指令',
            '',
            '🎮 祝您好运，玩得开心！',
        ];
    }

    public function cnHelp(int $userId, array $params, int $recordID): string|array
    {
        return CommandEnum::getHelpReply(true);
    }

    // --- 用户钱包指令 ---

    /**
     * /bindwallet - 绑定钱包
     */
    public function BindWallet(int $userId, array $params, int $recordID): string|array
    {
        try {
            // 验证参数
            if (count($params) !== 1) {
                return ['❌ Invalid parameters', 'Usage: /bindwallet <wallet_address>'];
            }
            
            $walletAddress = $params[0];
            if (!Tron::isAddress($walletAddress)) {
                return ['❌ Invalid TRON wallet address'];
            }

            // 调用服务进行绑定
            return $this->playerWalletBindingService->bindWallet(
                $this->getChatId(),
                $userId,
                $this->telegramBot->UserName() ?? '',
                $this->telegramBot->FirstName() ?? '',
                $this->telegramBot->LastName() ?? '',
                $walletAddress
            );
        } catch (\Throwable $e) {
            $this->logger->error('Bind wallet failed', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ Bind wallet failed, please try again later'];
        }
    }

    /**
     * /unbindwallet - 解绑钱包
     */
    public function UnbindWallet(int $userId, array $params, int $recordID): string|array
    {
        try {
            return $this->playerWalletBindingService->unbindWallet($this->getChatId(), $userId);
        } catch (\Throwable $e) {
            $this->logger->error('Unbind wallet failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Unbind wallet failed, please try again later'];
        }
    }

    /**
     * /mywallet - 我的钱包
     */
    public function MyWallet(int $userId, array $params, int $recordID): string|array
    {
        try {
            return $this->playerWalletBindingService->getMyWallet($this->getChatId(), $userId);
        } catch (\Throwable $e) {
            $this->logger->error('Query wallet failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Query wallet failed, please try again later'];
        }
    }

    // 中文指令适配 - 返回中文消息
    public function cnBindWallet(int $userId, array $params, int $recordID): string|array
    {
        try {
            // 验证参数
            if (count($params) !== 1) {
                return ['❌ 参数错误', '用法：/绑定钱包 <钱包地址>'];
            }
            
            $walletAddress = $params[0];
            if (!Tron::isAddress($walletAddress)) {
                return ['❌ 无效的TRON钱包地址'];
            }

            // 调用服务进行绑定 - 需要中文版本
            $result = $this->playerWalletBindingService->bindWallet(
                $this->getChatId(),
                $userId,
                $this->telegramBot->UserName() ?? '',
                $this->telegramBot->FirstName() ?? '',
                $this->telegramBot->LastName() ?? '',
                $walletAddress
            );
            
            // 将英文结果转换为中文
            return $this->translateToChineseResult($result, 'bindwallet');
        } catch (\Throwable $e) {
            $this->logger->error('绑定钱包失败', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ 绑定钱包失败，请稍后重试'];
        }
    }

    public function cnUnbindWallet(int $userId, array $params, int $recordID): string|array
    {
        try {
            $result = $this->playerWalletBindingService->unbindWallet($this->getChatId(), $userId);
            return $this->translateToChineseResult($result, 'unbindwallet');
        } catch (\Throwable $e) {
            $this->logger->error('解绑钱包失败', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 解绑钱包失败，请稍后重试'];
        }
    }

    public function cnMyWallet(int $userId, array $params, int $recordID): string|array
    {
        try {
            $result = $this->playerWalletBindingService->getMyWallet($this->getChatId(), $userId);
            return $this->translateToChineseResult($result, 'mywallet');
        } catch (\Throwable $e) {
            $this->logger->error('查询钱包失败', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 查询钱包失败，请稍后重试'];
        }
    }


    // --- 游戏查询指令 ---

    /**
     * /snake - 查看蛇身状态
     */
    public function Snake(int $userId, array $params, int $recordID): string|array
    {
        try {
            $result = $this->snakeService->getCurrentSnakeInfo($this->getChatId());
            // 确保返回英文消息
            return $this->translateToEnglishResult($result, 'snake');
        } catch (\Throwable $e) {
            $this->logger->error('Query snake status failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Query snake status failed, please try again later'];
        }
    }

    /**
     * /mytickets - 我的购彩记录
     */
    public function MyTickets(int $userId, array $params, int $recordID): string|array
    {
        try {
            $binding = $this->playerWalletBindingRepository->query()
                ->where('group_id', $this->getChatId())
                ->where('tg_user_id', $userId)
                ->first();
            
            if (!$binding) {
                return ['❌ You have no wallet address bound in this group', 'Please use /bindwallet <address> to bind'];
            }
            
            $nodes = $this->snakeNodeRepository->query()
                ->where('group_id', $this->getChatId())
                ->where('player_address', $binding->wallet_address)
                ->orderByDesc('id')
                ->limit(10)
                ->get();
            
            if ($nodes->isEmpty()) {
                return ['📝 You have no ticket records in this group'];
            }
            
            $reply = ['📝 Your last 10 ticket records:', ''];
            foreach ($nodes as $node) {
                $reply[] = sprintf(
                    '🎫 %s: %s (%s TRX) - %s',
                    $node->ticket_serial_no,
                    $node->ticket_number,
                    $node->amount,
                    $node->created_at->format('m-d H:i')
                );
            }
            return $reply;
        } catch (\Throwable $e) {
            $this->logger->error('Query ticket records failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Query ticket records failed, please try again later'];
        }
    }

    /**
     * /ticket - 查询票号详情
     */
    public function Ticket(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (count($params) !== 1) {
                return ['❌ Invalid parameters', 'Usage: /ticket <serial_no>'];
            }
            
            $serialNo = $params[0];
            $node = $this->snakeNodeRepository->query()->where('ticket_serial_no', $serialNo)->first();
            
            if (!$node) {
                return ['❌ Ticket record not found'];
            }
            
            return [
                '🎫 Ticket Details:',
                '',
                sprintf('Serial No: %s', $node->ticket_serial_no),
                sprintf('Ticket Number: %s', $node->ticket_number),
                sprintf('Player Address: %s', $node->player_address),
                sprintf('Bet Amount: %s TRX', $node->amount),
                sprintf('Purchase Time: %s', $node->created_at->format('Y-m-d H:i:s')),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Query ticket details failed', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ Query ticket details failed, please try again later'];
        }
    }

    /**
     * /myprizes - 我的中奖记录
     */
    public function MyPrizes(int $userId, array $params, int $recordID): string|array
    {
        try {
            $binding = $this->playerWalletBindingRepository->query()
                ->where('group_id', $this->getChatId())
                ->where('tg_user_id', $userId)
                ->first();
            
            if (!$binding) {
                return ['❌ You have no wallet address bound in this group', 'Please use /bindwallet <address> to bind'];
            }

            // 通过钱包地址查找中奖记录
            $winnerNodes = $this->snakeNodeRepository->query()
                ->where('group_id', $this->getChatId())
                ->where('player_address', $binding->wallet_address)
                ->where('is_winner', 1)
                ->orderByDesc('id')
                ->limit(10)
                ->get();

            if ($winnerNodes->isEmpty()) {
                return ['🎉 You have no prize records in this group', 'Keep trying!'];
            }

            $reply = ['🎉 Your last 10 prize records:', ''];
            foreach ($winnerNodes as $node) {
                $reply[] = sprintf(
                    '🏆 %s: %s - %s',
                    $node->ticket_serial_no,
                    $node->ticket_number,
                    $node->created_at->format('m-d H:i')
                );
            }
            return $reply;
        } catch (\Throwable $e) {
            $this->logger->error('Query prize records failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Query prize records failed, please try again later'];
        }
    }

    /**
     * /history - 历史中奖记录
     */
    public function History(int $userId, array $params, int $recordID): string|array
    {
        try {
            $records = $this->prizeRecordRepository->query()
                ->where('group_id', $this->getChatId())
                ->orderByDesc('id')
                ->limit(10)
                ->get();
            
            if ($records->isEmpty()) {
                return ['📊 No prize records found in this group'];
            }
            
            $reply = ['📊 Last 10 prize records:', ''];
            foreach ($records as $record) {
                $reply[] = sprintf(
                    '🏆 %s: %s TRX - %s',
                    $record->prize_serial_no,
                    $record->prize_amount,
                    $record->created_at->format('m-d H:i')
                );
            }
            return $reply;
        } catch (\Throwable $e) {
            $this->logger->error('Query history prize records failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Query history prize records failed, please try again later'];
        }
    }

    /**
     * /stats - 游戏统计
     */
    public function Stats(int $userId, array $params, int $recordID): string|array
    {
        try {
            $gameGroup = $this->gameGroupRepository->query()->where('tg_chat_id', $this->getChatId())->first();
            if (!$gameGroup) {
                return ['❌ Game not configured for this group'];
            }

            $totalNodes = $this->snakeNodeRepository->query()->where('group_id', $gameGroup->id)->count();
            $totalPrizes = $this->prizeRecordRepository->query()->where('group_id', $gameGroup->id)->count();
            $totalPrizeAmount = $this->prizeRecordRepository->query()->where('group_id', $gameGroup->id)->sum('prize_amount');

            return [
                '📊 Game Statistics',
                '--------------------',
                sprintf('🎫 Total Tickets: %d', $totalNodes),
                sprintf('🏆 Total Prizes: %d', $totalPrizes),
                sprintf('💰 Total Prize Amount: %s TRX', $totalPrizeAmount ?? '0.00'),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Query game statistics failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Query game statistics failed, please try again later'];
        }
    }

    /**
     * /rules - 游戏规则
     */
    public function Rules(int $userId, array $params, int $recordID): string|array
    {
        return [
            '📖 Snake Chain Game Rules',
            '--------------------',
            '1. Send specified amount of TRX to group payment address to buy tickets',
            '2. System generates 2-digit ticket number from your transaction hash',
            '3. Tickets form snake chain in chronological order',
            '4. 🎯 Super Prize: Same ticket number as previous one wins entire prize pool',
            '5. 🎁 Regular Prize: Matching ticket number with any in snake splits prize pool',
            '',
            '💡 Tip: Consecutive or interval matching ticket numbers win prizes!'
        ];
    }

    /**
     * /address - 收款地址
     */
    public function Address(int $userId, array $params, int $recordID): string|array
    {
        try {
            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                return ['❌ Game not configured for this group'];
            }
            
            return [
                '💰 Group Payment Address:',
                '<code>' . $config->wallet_address . '</code>',
                '',
                sprintf('Please send %s TRX to this address to play', $config->bet_amount)
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Query payment address failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Query payment address failed, please try again later'];
        }
    }

    // 中文指令适配 - 返回中文消息
    public function cnSnake(int $userId, array $params, int $recordID): string|array
    {
        try {
            $result = $this->snakeService->getCurrentSnakeInfo($this->getChatId());
            return $this->translateToChineseResult($result, 'snake');
        } catch (\Throwable $e) {
            $this->logger->error('查询蛇身状态失败', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 查询蛇身状态失败，请稍后重试'];
        }
    }
    
    public function cnMyTickets(int $userId, array $params, int $recordID): string|array
    {
        try {
            $binding = $this->playerWalletBindingRepository->query()
                ->where('group_id', $this->getChatId())
                ->where('tg_user_id', $userId)
                ->first();
            
            if (!$binding) {
                return ['❌ 您尚未在此群组绑定钱包地址', '请使用 /绑定钱包 <地址> 进行绑定'];
            }
            
            $nodes = $this->snakeNodeRepository->query()
                ->where('group_id', $this->getChatId())
                ->where('player_address', $binding->wallet_address)
                ->orderByDesc('id')
                ->limit(10)
                ->get();
            
            if ($nodes->isEmpty()) {
                return ['📝 您在此群组暂无购彩记录'];
            }
            
            $reply = ['📝 您的最近10条购彩记录：', ''];
            foreach ($nodes as $node) {
                $reply[] = sprintf(
                    '🎫 %s: %s (%s TRX) - %s',
                    $node->ticket_serial_no,
                    $node->ticket_number,
                    $node->amount,
                    $node->created_at->format('m-d H:i')
                );
            }
            return $reply;
        } catch (\Throwable $e) {
            $this->logger->error('查询购彩记录失败', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 查询购彩记录失败，请稍后重试'];
        }
    }
    
    public function cnTicket(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (count($params) !== 1) {
                return ['❌ 参数错误', '用法：/查询票号 <流水号>'];
            }
            
            $serialNo = $params[0];
            $node = $this->snakeNodeRepository->query()->where('ticket_serial_no', $serialNo)->first();
            
            if (!$node) {
                return ['❌ 未找到该票号记录'];
            }
            
            return [
                '🎫 票号详情：',
                '',
                sprintf('流水号：%s', $node->ticket_serial_no),
                sprintf('票号：%s', $node->ticket_number),
                sprintf('玩家地址：%s', $node->player_address),
                sprintf('投注金额：%s TRX', $node->amount),
                sprintf('购买时间：%s', $node->created_at->format('Y-m-d H:i:s')),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('查询票号详情失败', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ 查询票号详情失败，请稍后重试'];
        }
    }
    
    public function cnMyPrizes(int $userId, array $params, int $recordID): string|array
    {
        try {
            $binding = $this->playerWalletBindingRepository->query()
                ->where('group_id', $this->getChatId())
                ->where('tg_user_id', $userId)
                ->first();
            
            if (!$binding) {
                return ['❌ 您尚未在此群组绑定钱包地址', '请使用 /绑定钱包 <地址> 进行绑定'];
            }

            // 通过钱包地址查找中奖记录
            $winnerNodes = $this->snakeNodeRepository->query()
                ->where('group_id', $this->getChatId())
                ->where('player_address', $binding->wallet_address)
                ->where('is_winner', 1)
                ->orderByDesc('id')
                ->limit(10)
                ->get();

            if ($winnerNodes->isEmpty()) {
                return ['🎉 您在此群组暂无中奖记录', '继续加油！'];
            }

            $reply = ['🎉 您的最近10条中奖记录：', ''];
            foreach ($winnerNodes as $node) {
                $reply[] = sprintf(
                    '🏆 %s: %s - %s',
                    $node->ticket_serial_no,
                    $node->ticket_number,
                    $node->created_at->format('m-d H:i')
                );
            }
            return $reply;
        } catch (\Throwable $e) {
            $this->logger->error('查询中奖记录失败', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 查询中奖记录失败，请稍后重试'];
        }
    }
    
    public function cnHistory(int $userId, array $params, int $recordID): string|array
    {
        try {
            $records = $this->prizeRecordRepository->query()
                ->where('group_id', $this->getChatId())
                ->orderByDesc('id')
                ->limit(10)
                ->get();
            
            if ($records->isEmpty()) {
                return ['📊 此群组暂无中奖记录'];
            }
            
            $reply = ['📊 最近10条中奖记录：', ''];
            foreach ($records as $record) {
                $reply[] = sprintf(
                    '🏆 %s: %s TRX - %s',
                    $record->prize_serial_no,
                    $record->prize_amount,
                    $record->created_at->format('m-d H:i')
                );
            }
            return $reply;
        } catch (\Throwable $e) {
            $this->logger->error('查询历史中奖记录失败', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 查询历史中奖记录失败，请稍后重试'];
        }
    }
    
    public function cnStats(int $userId, array $params, int $recordID): string|array
    {
        try {
            $gameGroup = $this->gameGroupRepository->query()->where('tg_chat_id', $this->getChatId())->first();
            if (!$gameGroup) {
                return ['❌ 此群组未配置游戏'];
            }

            $totalNodes = $this->snakeNodeRepository->query()->where('group_id', $gameGroup->id)->count();
            $totalPrizes = $this->prizeRecordRepository->query()->where('group_id', $gameGroup->id)->count();
            $totalPrizeAmount = $this->prizeRecordRepository->query()->where('group_id', $gameGroup->id)->sum('prize_amount');

            return [
                '📊 游戏统计数据',
                '--------------------',
                sprintf('🎫 总票数：%d', $totalNodes),
                sprintf('🏆 中奖次数：%d', $totalPrizes),
                sprintf('💰 总奖金：%s TRX', $totalPrizeAmount ?? '0.00'),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('查询游戏统计失败', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 查询游戏统计失败，请稍后重试'];
        }
    }
    
    public function cnRules(int $userId, array $params, int $recordID): string|array
    {
        return [
            '📖 贪吃蛇链上游戏规则',
            '--------------------',
            '1. 向群组收款地址发送指定金额的TRX购买彩票',
            '2. 系统从您的交易哈希生成2位数票号',
            '3. 票号按时间顺序形成蛇身链条',
            '4. 🎯 超级大奖：票号与前一张相同，独得整个奖池',
            '5. 🎁 普通奖：票号与蛇身中任意一张匹配，与匹配者平分奖池',
            '',
            '💡 提示：票号连续或区间匹配即可中奖！'
        ];
    }
    
    public function cnAddress(int $userId, array $params, int $recordID): string|array
    {
        try {
            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                return ['❌ 此群组未配置游戏'];
            }
            
            return [
                '💰 群组收款地址：',
                '<code>' . $config->wallet_address . '</code>',
                '',
                sprintf('请发送 %s TRX 到此地址参与游戏', $config->bet_amount)
            ];
        } catch (\Throwable $e) {
            $this->logger->error('查询收款地址失败', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 查询收款地址失败，请稍后重试'];
        }
    }


    // --- 管理员指令 ---

    /**
     * /bind - 绑定租户
     */
    public function BindTenant(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDenied();
            }

            if (count($params) !== 1) {
                return ['❌ Invalid parameters', 'Usage: /bind <tenant_id>'];
            }
            
            $tenantId = $params[0];

            // 验证 tenant_id 是否存在
            $tenant = \Plugin\MineAdmin\Tenant\Model\Tenant::where('id', $tenantId)->first();
            if (!$tenant) {
                return ['❌ Tenant ID not found'];
            }

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                // 如果群组配置不存在，则创建一个新的
                $this->gameGroupConfigRepository->save([
                    'tenant_id' => $tenantId,
                    'tg_chat_id' => $this->getChatId(),
                    'tg_chat_title' => $this->telegramBot->getGroupTitle(),
                ]);
            } else {
                // 如果已存在，则更新
                $this->gameGroupConfigRepository->update($config->id, ['tenant_id' => $tenantId]);
            }

            return [sprintf('✅ Group successfully bound to tenant %s', $tenantId)];
        } catch (\Throwable $e) {
            $this->logger->error('Bind tenant failed', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ Bind tenant failed, please try again later'];
        }
    }

    /**
     * /wallet - 设置钱包
     */
    public function SetWallet(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDenied();
            }

            if (count($params) !== 1 || !Tron::isAddress($params[0])) {
                return ['❌ Invalid parameters', 'Usage: /wallet <new_wallet_address>'];
            }
            
            $newWalletAddress = $params[0];

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                return ['❌ Game not configured for this group, please bind tenant first'];
            }

            if ($config->wallet_change_status === GameGroupConfig::WALLET_CHANGE_STATUS_CHANGING) {
                return ['⚠️ Wallet change process is in progress', 'Please cancel it first or wait for completion'];
            }

            $this->gameGroupConfigRepository->update($config->id, [
                'pending_wallet_address' => $newWalletAddress,
                'wallet_change_status' => GameGroupConfig::WALLET_CHANGE_STATUS_CHANGING,
                'wallet_change_start_at' => date('Y-m-d H:i:s'),
                'wallet_change_end_at' => date('Y-m-d H:i:s', time() + 600), // 10分钟冷却期
            ]);

            return [
                '⚠️ IMPORTANT: Wallet change process initiated',
                sprintf('New Wallet Address: `%s`', $newWalletAddress),
                'Change will be effective in 10 minutes',
                'All ongoing games will be terminated and prize pool reset',
                'You can use /cancelwallet to abort this process',
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Set wallet failed', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ Set wallet failed, please try again later'];
        }
    }

    /**
     * /cancelwallet - 取消钱包变更
     */
    public function CancelWallet(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDenied();
            }

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config || $config->wallet_change_status !== GameGroupConfig::WALLET_CHANGE_STATUS_CHANGING) {
                return ['❌ No wallet change process in progress'];
            }

            $this->gameGroupConfigRepository->update($config->id, [
                'pending_wallet_address' => null,
                'wallet_change_status' => GameGroupConfig::WALLET_CHANGE_STATUS_NORMAL,
                'wallet_change_start_at' => null,
                'wallet_change_end_at' => null,
            ]);

            return ['✅ Wallet change process has been canceled'];
        } catch (\Throwable $e) {
            $this->logger->error('Cancel wallet change failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Cancel wallet change failed, please try again later'];
        }
    }

    /**
     * /setbet - 设置投注金额
     */
    public function SetBet(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDenied();
            }

            if (count($params) !== 1 || !is_numeric($params[0]) || (float)$params[0] <= 0) {
                return ['❌ Invalid parameters', 'Usage: /setbet <amount> (amount must be positive number)'];
            }
            
            $betAmount = (float)$params[0];

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                return ['❌ Game not configured for this group'];
            }

            $this->gameGroupConfigRepository->update($config->id, ['bet_amount' => $betAmount]);

            return [sprintf('✅ Bet amount set to %s TRX', $betAmount)];
        } catch (\Throwable $e) {
            $this->logger->error('Set bet amount failed', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ Set bet amount failed, please try again later'];
        }
    }

    /**
     * /setfee - 设置手续费
     */
    public function SetFee(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDenied();
            }

            if (count($params) !== 1 || !is_numeric($params[0]) || (float)$params[0] < 0 || (float)$params[0] > 50) {
                return ['❌ Invalid parameters', 'Usage: /setfee <rate> (rate must be between 0-50)'];
            }
            
            $feeRate = (float)$params[0] / 100;

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                return ['❌ Game not configured for this group'];
            }

            $this->gameGroupConfigRepository->update($config->id, ['platform_fee_rate' => $feeRate]);

            return [sprintf('✅ Platform fee rate set to %s%%', $params[0])];
        } catch (\Throwable $e) {
            $this->logger->error('Set fee rate failed', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ Set fee rate failed, please try again later'];
        }
    }

    /**
     * /info - 群组配置信息
     */
    public function Info(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDenied();
            }

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                return ['❌ Game not configured for this group'];
            }

            $reply = [
                '⚙️ Group Configuration',
                '--------------------',
                sprintf('Group Name: %s', $config->tg_chat_title),
                sprintf('Bet Amount: %s TRX', $config->bet_amount),
                sprintf('Platform Fee: %s%%', bcmul($config->platform_fee_rate, '100', 2)),
                sprintf('Wallet Address: `%s`', $config->wallet_address),
                sprintf('Wallet Status: %s', $config->wallet_change_status === GameGroupConfig::WALLET_CHANGE_STATUS_NORMAL ? 'Normal' : 'Changing'),
            ];
            
            if ($config->wallet_change_status === GameGroupConfig::WALLET_CHANGE_STATUS_CHANGING) {
                $reply[] = sprintf('New Wallet Address: `%s`', $config->pending_wallet_address);
                $reply[] = sprintf('Change Effective At: %s', $config->wallet_change_end_at->format('Y-m-d H:i:s'));
            }
            
            return $reply;
        } catch (\Throwable $e) {
            $this->logger->error('Query group configuration failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Query group configuration failed, please try again later'];
        }
    }

    // 中文指令适配 - 返回中文消息
    public function cnBindTenant(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDeniedCn();
            }

            if (count($params) !== 1) {
                return ['❌ 参数错误', '用法：/绑定租户 <租户ID>'];
            }
            
            $tenantId = $params[0];

            // 验证 tenant_id 是否存在
            $tenant = \Plugin\MineAdmin\Tenant\Model\Tenant::where('id', $tenantId)->first();
            if (!$tenant) {
                return ['❌ 租户ID不存在'];
            }

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                // 如果群组配置不存在，则创建一个新的
                $this->gameGroupConfigRepository->save([
                    'tenant_id' => $tenantId,
                    'tg_chat_id' => $this->getChatId(),
                    'tg_chat_title' => $this->telegramBot->getGroupTitle(),
                ]);
            } else {
                // 如果已存在，则更新
                $this->gameGroupConfigRepository->update($config->id, ['tenant_id' => $tenantId]);
            }

            return [sprintf('✅ 群组已成功绑定到租户 %s', $tenantId)];
        } catch (\Throwable $e) {
            $this->logger->error('绑定租户失败', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ 绑定租户失败，请稍后重试'];
        }
    }
    
    public function cnSetWallet(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDeniedCn();
            }

            if (count($params) !== 1 || !Tron::isAddress($params[0])) {
                return ['❌ 参数错误', '用法：/设置钱包 <新钱包地址>'];
            }
            
            $newWalletAddress = $params[0];

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                return ['❌ 群组未配置游戏，请先绑定租户'];
            }

            if ($config->wallet_change_status === GameGroupConfig::WALLET_CHANGE_STATUS_CHANGING) {
                return ['⚠️ 钱包变更正在进行中', '请先取消或等待完成'];
            }

            $this->gameGroupConfigRepository->update($config->id, [
                'pending_wallet_address' => $newWalletAddress,
                'wallet_change_status' => GameGroupConfig::WALLET_CHANGE_STATUS_CHANGING,
                'wallet_change_start_at' => date('Y-m-d H:i:s'),
                'wallet_change_end_at' => date('Y-m-d H:i:s', time() + 600), // 10分钟冷却期
            ]);

            return [
                '⚠️ 重要：钱包变更流程已启动',
                sprintf('新钱包地址：`%s`', $newWalletAddress),
                '变更将在10分钟后生效',
                '所有进行中的游戏将终止，奖池将重置',
                '您可以使用 /取消钱包变更 取消此流程',
            ];
        } catch (\Throwable $e) {
            $this->logger->error('设置钱包失败', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ 设置钱包失败，请稍后重试'];
        }
    }
    
    public function cnCancelWallet(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDeniedCn();
            }

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config || $config->wallet_change_status !== GameGroupConfig::WALLET_CHANGE_STATUS_CHANGING) {
                return ['❌ 没有进行中的钱包变更流程'];
            }

            $this->gameGroupConfigRepository->update($config->id, [
                'pending_wallet_address' => null,
                'wallet_change_status' => GameGroupConfig::WALLET_CHANGE_STATUS_NORMAL,
                'wallet_change_start_at' => null,
                'wallet_change_end_at' => null,
            ]);

            return ['✅ 钱包变更流程已取消'];
        } catch (\Throwable $e) {
            $this->logger->error('取消钱包变更失败', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 取消钱包变更失败，请稍后重试'];
        }
    }
    
    public function cnSetBet(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDeniedCn();
            }

            if (count($params) !== 1 || !is_numeric($params[0]) || (float)$params[0] <= 0) {
                return ['❌ 参数错误', '用法：/设置投注 <金额> (金额必须为正数)'];
            }
            
            $betAmount = (float)$params[0];

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                return ['❌ 群组未配置游戏'];
            }

            $this->gameGroupConfigRepository->update($config->id, ['bet_amount' => $betAmount]);

            return [sprintf('✅ 投注金额已设置为 %s TRX', $betAmount)];
        } catch (\Throwable $e) {
            $this->logger->error('设置投注金额失败', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ 设置投注金额失败，请稍后重试'];
        }
    }
    
    public function cnSetFee(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDeniedCn();
            }

            if (count($params) !== 1 || !is_numeric($params[0]) || (float)$params[0] < 0 || (float)$params[0] > 50) {
                return ['❌ 参数错误', '用法：/设置手续费 <比例> (比例必须在0-50之间)'];
            }
            
            $feeRate = (float)$params[0] / 100;

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                return ['❌ 群组未配置游戏'];
            }

            $this->gameGroupConfigRepository->update($config->id, ['platform_fee_rate' => $feeRate]);

            return [sprintf('✅ 平台手续费已设置为 %s%%', $params[0])];
        } catch (\Throwable $e) {
            $this->logger->error('设置手续费失败', [
                'user_id' => $userId,
                'params' => json_encode($params),
                'error' => $e->getMessage()
            ]);
            return ['❌ 设置手续费失败，请稍后重试'];
        }
    }
    
    public function cnInfo(int $userId, array $params, int $recordID): string|array
    {
        try {
            if (!$this->isGroupAdmin()) {
                return $this->adminPermissionDeniedCn();
            }

            $config = $this->gameGroupConfigRepository->getConfigByChatId($this->getChatId());
            if (!$config) {
                return ['❌ 群组未配置游戏'];
            }

            $reply = [
                '⚙️ 群组配置信息',
                '--------------------',
                sprintf('群组名称：%s', $config->tg_chat_title),
                sprintf('投注金额：%s TRX', $config->bet_amount),
                sprintf('平台手续费：%s%%', bcmul($config->platform_fee_rate, '100', 2)),
                sprintf('钱包地址：`%s`', $config->wallet_address),
                sprintf('钱包状态：%s', $config->wallet_change_status === GameGroupConfig::WALLET_CHANGE_STATUS_NORMAL ? '正常' : '变更中'),
            ];
            
            if ($config->wallet_change_status === GameGroupConfig::WALLET_CHANGE_STATUS_CHANGING) {
                $reply[] = sprintf('新钱包地址：`%s`', $config->pending_wallet_address);
                $reply[] = sprintf('变更生效时间：%s', $config->wallet_change_end_at->format('Y-m-d H:i:s'));
            }
            
            return $reply;
        } catch (\Throwable $e) {
            $this->logger->error('查询群组配置失败', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 查询群组配置失败，请稍后重试'];
        }
    }
}