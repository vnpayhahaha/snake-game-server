<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Game\GameGroupConfig;
use App\Model\Game\GameGroup;
use App\Model\Player\PlayerWalletBinding;
use App\Model\Snake\SnakeNode;
use App\Model\Prize\PrizeRecord;
use App\Repository\Telegram\TelegramCommandMessageRecordRepository;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\DbConnection\Db;

#[Command]
class InitMockDataCommand extends HyperfCommand
{
    #[Inject]
    protected StdoutLoggerInterface $logger;

    #[Inject]
    protected TelegramCommandMessageRecordRepository $telegramCommandMessageRecordRepository;

    public function __construct()
    {
        parent::__construct('mock:init');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Initialize mock data for Telegram bot testing');
    }

    public function handle()
    {
        $this->info('🔧 Initializing mock data for Telegram bot testing...');
        $this->line(str_repeat('=', 60));

        try {
            Db::beginTransaction();

            // 清理现有测试数据
            $this->cleanupTestData();

            // 创建测试数据
            $configId = $this->createGameGroupConfig();
            $this->createGameGroup($configId);
            $this->createPlayerWalletBindings($configId);
            $this->createSnakeNodes($configId);
            $this->createPrizeRecords($configId);
            $this->createTelegramCommandRecords();

            Db::commit();

            $this->info('✅ Mock data initialized successfully!');
            $this->showDataSummary($configId);

        } catch (\Throwable $e) {
            Db::rollBack();
            $this->error('❌ Failed to initialize mock data: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }

        return 0;
    }

    /**
     * 清理测试数据
     */
    private function cleanupTestData(): void
    {
        $this->comment('🧹 Cleaning up existing test data...');

        $testChatId = -1001234567890;

        // 获取要删除的配置ID
        $configIds = GameGroupConfig::where('tg_chat_id', $testChatId)->pluck('id')->toArray();

        if (!empty($configIds)) {
            // 删除相关数据
            PrizeRecord::whereIn('group_id', $configIds)->delete();
            SnakeNode::whereIn('group_id', $configIds)->delete();
            PlayerWalletBinding::whereIn('group_id', $configIds)->delete();
            GameGroup::whereIn('config_id', $configIds)->delete();
            GameGroupConfig::whereIn('id', $configIds)->delete();
        }

        // 删除测试消息记录
        $this->telegramCommandMessageRecordRepository->getModel()
            ->where('chat_id', $testChatId)
            ->delete();

        $this->line('✅ Cleanup completed');
    }

    /**
     * 创建游戏群组配置
     */
    private function createGameGroupConfig(): int
    {
        $this->comment('📝 Creating game group config...');

        $config = GameGroupConfig::create([
            'tenant_id' => '000001',
            'tg_chat_id' => -1001234567890,
            'tg_chat_title' => 'Test Snake Game Group',
            'wallet_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx',
            'wallet_change_count' => 0,
            'pending_wallet_address' => null,
            'wallet_change_status' => GameGroupConfig::WALLET_CHANGE_STATUS_NORMAL,
            'wallet_change_start_at' => null,
            'wallet_change_end_at' => null,
            'hot_wallet_address' => 'TTestHotWallet123456789012345678901',
            'hot_wallet_private_key' => 'encrypted_private_key_here',
            'bet_amount' => 5.00,
            'platform_fee_rate' => 0.10,
            'status' => 1,
        ]);

        $this->line('✅ Game group config created with ID: ' . $config->id);
        return $config->id;
    }

    /**
     * 创建游戏群组状态
     */
    private function createGameGroup(int $configId): void
    {
        $this->comment('📝 Creating game group state...');

        GameGroup::create([
            'config_id' => $configId,
            'tg_chat_id' => -1001234567890,
            'prize_pool_amount' => 25.50,
            'current_snake_nodes' => json_encode([1, 2, 3, 4, 5]),
            'last_snake_nodes' => json_encode([1, 2, 3]),
            'last_prize_nodes' => json_encode([2, 3]),
            'last_prize_amount' => 15.30,
            'last_prize_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx,TTestWallet2',
            'last_prize_serial_no' => '20250108-001',
            'last_prize_at' => now()->subHour(),
            'version' => 1,
        ]);

        $this->line('✅ Game group state created');
    }

    /**
     * 创建玩家钱包绑定
     */
    private function createPlayerWalletBindings(int $configId): void
    {
        $this->comment('📝 Creating player wallet bindings...');

        $bindings = [
            [
                'group_id' => $configId,
                'tg_user_id' => 123456789,
                'tg_username' => 'testuser',
                'tg_first_name' => 'Test',
                'tg_last_name' => 'User',
                'wallet_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx',
                'bind_at' => now()->subDay(),
            ],
            [
                'group_id' => $configId,
                'tg_user_id' => 987654321,
                'tg_username' => 'testuser2',
                'tg_first_name' => 'Test2',
                'tg_last_name' => 'User2',
                'wallet_address' => 'TTestWallet2345678901234567890123',
                'bind_at' => now()->subDays(2),
            ],
        ];

        foreach ($bindings as $binding) {
            PlayerWalletBinding::create($binding);
        }

        $this->line('✅ Created ' . count($bindings) . ' player wallet bindings');
    }

    /**
     * 创建蛇身节点
     */
    private function createSnakeNodes(int $configId): void
    {
        $this->comment('📝 Creating snake nodes...');

        $nodes = [
            [
                'group_id' => $configId,
                'ticket_serial_no' => '20250108-001',
                'ticket_number' => '09',
                'player_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx',
                'player_tg_user_id' => 123456789,
                'player_tg_username' => 'testuser',
                'amount' => 5.00,
                'tx_hash' => 'mock_tx_hash_001',
                'block_height' => 45678901,
                'block_timestamp' => now()->subHours(5),
                'is_winner' => 1,
                'prize_amount' => 7.50,
            ],
            [
                'group_id' => $configId,
                'ticket_serial_no' => '20250108-002',
                'ticket_number' => '15',
                'player_address' => 'TTestWallet2345678901234567890123',
                'player_tg_user_id' => 987654321,
                'player_tg_username' => 'testuser2',
                'amount' => 5.00,
                'tx_hash' => 'mock_tx_hash_002',
                'block_height' => 45678902,
                'block_timestamp' => now()->subHours(4),
                'is_winner' => 0,
                'prize_amount' => 0.00,
            ],
            [
                'group_id' => $configId,
                'ticket_serial_no' => '20250108-003',
                'ticket_number' => '09',
                'player_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx',
                'player_tg_user_id' => 123456789,
                'player_tg_username' => 'testuser',
                'amount' => 5.00,
                'tx_hash' => 'mock_tx_hash_003',
                'block_height' => 45678903,
                'block_timestamp' => now()->subHours(3),
                'is_winner' => 1,
                'prize_amount' => 7.80,
            ],
            [
                'group_id' => $configId,
                'ticket_serial_no' => '20250108-004',
                'ticket_number' => '22',
                'player_address' => 'TTestWallet2345678901234567890123',
                'player_tg_user_id' => 987654321,
                'player_tg_username' => 'testuser2',
                'amount' => 5.00,
                'tx_hash' => 'mock_tx_hash_004',
                'block_height' => 45678904,
                'block_timestamp' => now()->subHours(2),
                'is_winner' => 0,
                'prize_amount' => 0.00,
            ],
            [
                'group_id' => $configId,
                'ticket_serial_no' => '20250108-005',
                'ticket_number' => '33',
                'player_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx',
                'player_tg_user_id' => 123456789,
                'player_tg_username' => 'testuser',
                'amount' => 5.00,
                'tx_hash' => 'mock_tx_hash_005',
                'block_height' => 45678905,
                'block_timestamp' => now()->subHour(),
                'is_winner' => 0,
                'prize_amount' => 0.00,
            ],
        ];

        foreach ($nodes as $node) {
            SnakeNode::create($node);
        }

        $this->line('✅ Created ' . count($nodes) . ' snake nodes');
    }

    /**
     * 创建中奖记录
     */
    private function createPrizeRecords(int $configId): void
    {
        $this->comment('📝 Creating prize records...');

        $records = [
            [
                'group_id' => $configId,
                'prize_serial_no' => '20250108-001',
                'ticket_number' => '09',
                'winner_node_ids' => json_encode([1, 3]),
                'winner_count' => 2,
                'prize_amount' => 15.30,
                'prize_per_winner' => 7.65,
                'platform_fee' => 1.70,
                'status' => 2, // 已完成
            ],
            [
                'group_id' => $configId,
                'prize_serial_no' => '20250108-002',
                'ticket_number' => '15',
                'winner_node_ids' => json_encode([2]),
                'winner_count' => 1,
                'prize_amount' => 9.00,
                'prize_per_winner' => 9.00,
                'platform_fee' => 1.00,
                'status' => 2, // 已完成
            ],
        ];

        foreach ($records as $record) {
            PrizeRecord::create($record);
        }

        $this->line('✅ Created ' . count($records) . ' prize records');
    }

    /**
     * 创建Telegram指令消息记录
     */
    private function createTelegramCommandRecords(): void
    {
        $this->comment('📝 Creating telegram command records...');

        $records = [
            [
                'chat_id' => -1001234567890,
                'message_id' => 12345,
                'command' => 'start',
                'chat_name' => 'Test Snake Game Group',
                'user_id' => 123456789,
                'username' => 'testuser',
                'nickname' => 'Test User',
                'original_message' => '/start',
                'response_message' => 'Welcome to Snake Chain Game!',
                'status' => 2, // 已完成
            ],
            [
                'chat_id' => -1001234567890,
                'message_id' => 12346,
                'command' => 'bindwallet',
                'chat_name' => 'Test Snake Game Group',
                'user_id' => 123456789,
                'username' => 'testuser',
                'nickname' => 'Test User',
                'original_message' => '/bindwallet TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx',
                'response_message' => 'Wallet address bound successfully!',
                'status' => 2, // 已完成
            ],
        ];

        foreach ($records as $record) {
            $this->telegramCommandMessageRecordRepository->save($record);
        }

        $this->line('✅ Created ' . count($records) . ' telegram command records');
    }

    /**
     * 显示数据摘要
     */
    private function showDataSummary(int $configId): void
    {
        $this->line('');
        $this->info('📊 Mock Data Summary:');
        $this->line(str_repeat('-', 40));

        // 统计数据
        $configCount = GameGroupConfig::where('id', $configId)->count();
        $groupCount = GameGroup::where('config_id', $configId)->count();
        $bindingCount = PlayerWalletBinding::where('group_id', $configId)->count();
        $nodeCount = SnakeNode::where('group_id', $configId)->count();
        $prizeCount = PrizeRecord::where('group_id', $configId)->count();
        $commandCount = $this->telegramCommandMessageRecordRepository->getModel()
            ->where('chat_id', -1001234567890)->count();

        $this->line("📋 Game Group Configs: {$configCount}");
        $this->line("🎮 Game Groups: {$groupCount}");
        $this->line("💰 Player Wallet Bindings: {$bindingCount}");
        $this->line("🐍 Snake Nodes: {$nodeCount}");
        $this->line("🏆 Prize Records: {$prizeCount}");
        $this->line("📱 Command Records: {$commandCount}");

        $this->line('');
        $this->info('🧪 Test Environment Ready!');
        $this->line('Test Chat ID: -1001234567890');
        $this->line('Test User IDs: 123456789, 987654321');
        $this->line('Test Wallet: TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx');
        
        $this->line('');
        $this->comment('💡 Run tests with:');
        $this->line('php bin/hyperf.php telegram:test');
        $this->line('php bin/hyperf.php telegram:test start');
        $this->line('php bin/hyperf.php telegram:test --lang=cn');
    }
}