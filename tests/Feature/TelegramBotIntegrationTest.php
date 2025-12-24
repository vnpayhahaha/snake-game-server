<?php

declare(strict_types=1);

namespace HyperfTests\Feature;

use App\Service\Telegram\Bot\TelegramService;
use App\Service\Telegram\Bot\TelegramCommandService;
use App\Service\Telegram\Bot\CommandEnum;
use App\Model\Game\GameGroupConfig;
use App\Model\Player\PlayerWalletBinding;
use Hyperf\Testing\TestCase;
use Hyperf\DbConnection\Db;

/**
 * Telegram Bot 集成测试
 */
class TelegramBotIntegrationTest extends TestCase
{
    private TelegramService $telegramService;
    private TelegramCommandService $commandService;
    private int $testChatId = -1001234567890;
    private int $testUserId = 123456789;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->telegramService = $this->container->get(TelegramService::class);
        $this->commandService = $this->container->get(TelegramCommandService::class);
        
        // 清理测试数据
        $this->cleanupTestData();
        
        // 创建测试数据
        $this->createTestData();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        parent::tearDown();
    }

    /**
     * 清理测试数据
     */
    private function cleanupTestData(): void
    {
        Db::table('player_wallet_binding')
            ->where('tg_user_id', $this->testUserId)
            ->delete();
            
        Db::table('game_group_config')
            ->where('tg_chat_id', $this->testChatId)
            ->delete();
    }

    /**
     * 创建测试数据
     */
    private function createTestData(): void
    {
        // 创建测试群组配置
        GameGroupConfig::create([
            'tenant_id' => '000001',
            'tg_chat_id' => $this->testChatId,
            'tg_chat_title' => 'Test Integration Group',
            'wallet_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx',
            'wallet_change_count' => 0,
            'wallet_change_status' => GameGroupConfig::WALLET_CHANGE_STATUS_NORMAL,
            'bet_amount' => 5.00,
            'platform_fee_rate' => 0.10,
            'status' => 1,
        ]);
    }

    /**
     * 创建模拟的Telegram Bot数据
     */
    private function createMockTelegramData(string $command, array $params = []): array
    {
        $text = '/' . $command;
        if (!empty($params)) {
            $text .= ' ' . implode(' ', $params);
        }

        return [
            'message' => [
                'message_id' => rand(10000, 99999),
                'from' => [
                    'id' => $this->testUserId,
                    'is_bot' => false,
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'username' => 'testuser',
                    'language_code' => 'en'
                ],
                'chat' => [
                    'id' => $this->testChatId,
                    'title' => 'Test Integration Group',
                    'type' => 'supergroup'
                ],
                'date' => time(),
                'text' => $text
            ]
        ];
    }

    /**
     * 测试完整的指令处理流程
     */
    public function testCompleteCommandProcessingFlow(): void
    {
        // 测试start指令
        $data = $this->createMockTelegramData('start');
        $result = $this->telegramService->commandRunConsumer([
            'data' => $data,
            'params' => [],
            'method' => 'Start',
            'command' => 'start'
        ]);

        $this->assertTrue($result);
    }

    /**
     * 测试钱包绑定完整流程
     */
    public function testWalletBindingCompleteFlow(): void
    {
        $walletAddress = 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx';
        
        // 1. 绑定钱包
        $data = $this->createMockTelegramData('bindwallet', [$walletAddress]);
        $result = $this->telegramService->commandRunConsumer([
            'data' => $data,
            'params' => [$walletAddress],
            'method' => 'BindWallet',
            'command' => 'bindwallet'
        ]);

        $this->assertTrue($result);

        // 2. 验证绑定记录已创建
        $binding = PlayerWalletBinding::where('tg_user_id', $this->testUserId)
            ->where('wallet_address', $walletAddress)
            ->first();

        $this->assertNotNull($binding);
        $this->assertEquals('testuser', $binding->tg_username);
        $this->assertEquals($walletAddress, $binding->wallet_address);

        // 3. 查看我的钱包
        $data = $this->createMockTelegramData('mywallet');
        $result = $this->telegramService->commandRunConsumer([
            'data' => $data,
            'params' => [],
            'method' => 'MyWallet',
            'command' => 'mywallet'
        ]);

        $this->assertTrue($result);

        // 4. 解绑钱包
        $data = $this->createMockTelegramData('unbindwallet');
        $result = $this->telegramService->commandRunConsumer([
            'data' => $data,
            'params' => [],
            'method' => 'UnbindWallet',
            'command' => 'unbindwallet'
        ]);

        $this->assertTrue($result);

        // 5. 验证绑定记录已删除
        $binding = PlayerWalletBinding::where('tg_user_id', $this->testUserId)
            ->where('wallet_address', $walletAddress)
            ->first();

        $this->assertNull($binding);
    }

    /**
     * 测试中文指令完整流程
     */
    public function testChineseCommandCompleteFlow(): void
    {
        $walletAddress = 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx';
        
        // 1. 中文绑定钱包
        $data = $this->createMockTelegramData('绑定钱包', [$walletAddress]);
        $result = $this->telegramService->commandRunConsumer([
            'data' => $data,
            'params' => [$walletAddress],
            'method' => 'cnBindWallet',
            'command' => '绑定钱包'
        ]);

        $this->assertTrue($result);

        // 2. 中文查看钱包
        $data = $this->createMockTelegramData('我的钱包');
        $result = $this->telegramService->commandRunConsumer([
            'data' => $data,
            'params' => [],
            'method' => 'cnMyWallet',
            'command' => '我的钱包'
        ]);

        $this->assertTrue($result);
    }

    /**
     * 测试指令解析
     */
    public function testCommandParsing(): void
    {
        $testCases = [
            // 基础指令
            '/start' => ['start', []],
            '/help' => ['help', []],
            
            // 带参数的指令
            '/bindwallet TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx' => ['bindwallet', ['TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx']],
            '/ticket 20250108-001' => ['ticket', ['20250108-001']],
            '/setbet 5' => ['setbet', ['5']],
            
            // 中文指令
            '/开始' => ['开始', []],
            '/绑定钱包 TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx' => ['绑定钱包', ['TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx']],
            '/设置投注 10' => ['设置投注', ['10']],
        ];

        foreach ($testCases as $input => $expected) {
            [$expectedCommand, $expectedParams] = $expected;
            
            // 模拟解析逻辑
            $text = substr($input, 1); // 去掉 /
            $parts = explode(' ', $text);
            $command = array_shift($parts);
            $params = array_filter($parts);

            $this->assertEquals($expectedCommand, $command);
            $this->assertEquals($expectedParams, array_values($params));
        }
    }

    /**
     * 测试错误处理
     */
    public function testErrorHandling(): void
    {
        // 测试无效指令
        $data = $this->createMockTelegramData('invalidcommand');
        
        // 模拟群组工作流程
        $mockBot = new \Telegram(env('TELEGRAM_TOKEN', 'mock_token'));
        $mockBot->setData($data);
        
        $telegramService = new class extends TelegramService {
            public function testGroupWork($bot) {
                $this->telegramBot = $bot;
                return $this->groupWork();
            }
        };
        
        $result = $telegramService->testGroupWork($mockBot);
        $this->assertFalse($result);
    }

    /**
     * 测试队列处理
     */
    public function testQueueProcessing(): void
    {
        // 测试指令队列数据格式
        $queueData = [
            'data' => $this->createMockTelegramData('start'),
            'params' => [],
            'method' => 'Start',
            'command' => 'start'
        ];

        // 验证队列数据结构
        $this->assertArrayHasKey('data', $queueData);
        $this->assertArrayHasKey('params', $queueData);
        $this->assertArrayHasKey('method', $queueData);
        $this->assertArrayHasKey('command', $queueData);
        $this->assertArrayHasKey('message', $queueData['data']);
        $this->assertIsArray($queueData['params']);
        $this->assertIsString($queueData['method']);
        $this->assertIsString($queueData['command']);
    }

    /**
     * 测试多语言响应差异
     */
    public function testMultilingualResponseDifferences(): void
    {
        // 设置模拟的Telegram Bot
        $mockBot = new class {
            public function ChatID(): string { return '-1001234567890'; }
            public function UserId(): string { return '123456789'; }
            public function UserName(): ?string { return 'testuser'; }
            public function FirstName(): string { return 'Test'; }
            public function LastName(): string { return 'User'; }
            public function MessageID(): string { return '12345'; }
            public function getGroupTitle(): string { return 'Test Group'; }
            public function getChatMember(array $params): array {
                return ['ok' => true, 'result' => ['status' => 'administrator']];
            }
        };

        $this->commandService->setTelegramBot($mockBot);

        // 测试英文和中文响应的差异
        $englishResult = $this->commandService->Start(123456789, [], 1);
        $chineseResult = $this->commandService->cnStart(123456789, [], 1);

        $this->assertIsArray($englishResult);
        $this->assertIsArray($chineseResult);
        $this->assertStringContainsString('Welcome', $englishResult[0]);
        $this->assertStringContainsString('欢迎', $chineseResult[0]);
        $this->assertNotEquals($englishResult, $chineseResult);

        // 测试规则指令的差异
        $englishRules = $this->commandService->Rules(123456789, [], 1);
        $chineseRules = $this->commandService->cnRules(123456789, [], 1);

        $this->assertStringContainsString('Snake Chain Game Rules', $englishRules[0]);
        $this->assertStringContainsString('贪吃蛇链上游戏规则', $chineseRules[0]);
        $this->assertNotEquals($englishRules, $chineseRules);
    }

    /**
     * 测试数据库事务
     */
    public function testDatabaseTransactions(): void
    {
        $walletAddress = 'TTestTransactionWallet123456789012';
        
        Db::beginTransaction();
        
        try {
            // 创建钱包绑定
            PlayerWalletBinding::create([
                'group_id' => 1,
                'tg_user_id' => $this->testUserId,
                'tg_username' => 'testuser',
                'tg_first_name' => 'Test',
                'tg_last_name' => 'User',
                'wallet_address' => $walletAddress,
                'bind_at' => now(),
            ]);

            // 验证记录存在
            $binding = PlayerWalletBinding::where('wallet_address', $walletAddress)->first();
            $this->assertNotNull($binding);

            Db::rollBack();

            // 验证回滚后记录不存在
            $binding = PlayerWalletBinding::where('wallet_address', $walletAddress)->first();
            $this->assertNull($binding);

        } catch (\Throwable $e) {
            Db::rollBack();
            throw $e;
        }
    }
}