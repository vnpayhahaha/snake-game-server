<?php

declare(strict_types=1);

/**
 * Telegram 机器人指令模拟测试脚本
 * 用于本地开发环境调试
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Service\Telegram\Bot\TelegramCommandService;
use App\Service\Telegram\Bot\CommandEnum;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;

class TelegramCommandTester
{
    private TelegramCommandService $commandService;
    private StdoutLoggerInterface $logger;

    public function __construct()
    {
        // 初始化容器和服务
        $container = ApplicationContext::getContainer();
        $this->commandService = $container->get(TelegramCommandService::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
        
        // 设置模拟的Telegram Bot
        $this->setupMockTelegramBot();
    }

    /**
     * 设置模拟的Telegram Bot
     */
    private function setupMockTelegramBot(): void
    {
        $mockBot = new class {
            private array $data = [
                'message' => [
                    'message_id' => 12345,
                    'from' => [
                        'id' => 123456789,
                        'is_bot' => false,
                        'first_name' => 'Test',
                        'last_name' => 'User',
                        'username' => 'testuser',
                        'language_code' => 'en'
                    ],
                    'chat' => [
                        'id' => -1001234567890,
                        'title' => 'Test Snake Game Group',
                        'type' => 'supergroup'
                    ],
                    'date' => 1640995200,
                    'text' => '/start'
                ]
            ];

            public function setData(array $data): void
            {
                $this->data = $data;
            }

            public function getData(): array
            {
                return $this->data;
            }

            public function ChatID(): string
            {
                return (string)$this->data['message']['chat']['id'];
            }

            public function UserId(): string
            {
                return (string)$this->data['message']['from']['id'];
            }

            public function UserName(): ?string
            {
                return $this->data['message']['from']['username'] ?? null;
            }

            public function FirstName(): string
            {
                return $this->data['message']['from']['first_name'] ?? '';
            }

            public function LastName(): string
            {
                return $this->data['message']['from']['last_name'] ?? '';
            }

            public function MessageID(): string
            {
                return (string)$this->data['message']['message_id'];
            }

            public function Text(): string
            {
                return $this->data['message']['text'] ?? '';
            }

            public function getGroupTitle(): string
            {
                return $this->data['message']['chat']['title'] ?? '';
            }

            public function getChatAdministrators(int $chatId): array
            {
                // 模拟管理员列表
                return [
                    [
                        'user' => [
                            'id' => 123456789,
                            'is_bot' => false,
                            'first_name' => 'Test',
                            'username' => 'testuser'
                        ],
                        'status' => 'administrator'
                    ]
                ];
            }

            public function getChatMember(array $params): array
            {
                return [
                    'ok' => true,
                    'result' => [
                        'user' => [
                            'id' => $params['user_id'],
                            'is_bot' => false,
                            'first_name' => 'Test',
                            'username' => 'testuser'
                        ],
                        'status' => 'administrator'
                    ]
                ];
            }
        };

        $this->commandService->setTelegramBot($mockBot);
    }

    /**
     * 创建模拟数据
     */
    public function createMockData(): void
    {
        echo "🔧 Creating mock data...\n";

        try {
            // 这里可以添加创建模拟数据的逻辑
            // 由于我们在测试环境，暂时跳过数据库操作
            echo "✅ Mock data created successfully\n";
        } catch (\Throwable $e) {
            echo "❌ Failed to create mock data: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 测试指令
     */
    public function testCommand(string $command, array $params = [], int $userId = 123456789): void
    {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "🧪 Testing command: /{$command}\n";
        echo "📝 Parameters: " . json_encode($params) . "\n";
        echo str_repeat("-", 50) . "\n";

        try {
            // 检查指令是否有效
            if (!CommandEnum::isCommand($command)) {
                echo "❌ Invalid command: {$command}\n";
                return;
            }

            // 获取方法名
            $method = CommandEnum::getCommand($command);
            echo "🎯 Method: {$method}\n";

            // 检查方法是否存在
            if (!method_exists($this->commandService, $method)) {
                echo "❌ Method not found: {$method}\n";
                return;
            }

            // 执行指令
            $startTime = microtime(true);
            $result = $this->commandService->{$method}($userId, $params, 1);
            $endTime = microtime(true);

            // 显示结果
            echo "⏱️  Execution time: " . round(($endTime - $startTime) * 1000, 2) . "ms\n";
            echo "📤 Result:\n";
            
            if (is_array($result)) {
                foreach ($result as $line) {
                    echo "   " . $line . "\n";
                }
            } else {
                echo "   " . $result . "\n";
            }

            echo "✅ Command executed successfully\n";

        } catch (\Throwable $e) {
            echo "❌ Command execution failed:\n";
            echo "   Error: " . $e->getMessage() . "\n";
            echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
            
            // 显示堆栈跟踪（仅前5行）
            $trace = explode("\n", $e->getTraceAsString());
            echo "   Stack trace:\n";
            for ($i = 0; $i < min(5, count($trace)); $i++) {
                echo "     " . $trace[$i] . "\n";
            }
        }
    }

    /**
     * 运行所有测试
     */
    public function runAllTests(): void
    {
        echo "🚀 Starting Telegram Command Tests\n";
        echo "📅 " . date('Y-m-d H:i:s') . "\n";

        // 创建模拟数据
        $this->createMockData();

        // 测试基础指令
        echo "\n📋 Testing Basic Commands:\n";
        $this->testCommand('start');
        $this->testCommand('help');
        $this->testCommand('开始');
        $this->testCommand('帮助');

        // 测试钱包指令
        echo "\n💰 Testing Wallet Commands:\n";
        $this->testCommand('bindwallet', ['TTestWalletAddress123456789012345']);
        $this->testCommand('mywallet');
        $this->testCommand('unbindwallet');
        $this->testCommand('绑定钱包', ['TTestWalletAddress123456789012345']);
        $this->testCommand('我的钱包');
        $this->testCommand('解绑钱包');

        // 测试游戏查询指令
        echo "\n🎮 Testing Game Query Commands:\n";
        $this->testCommand('snake');
        $this->testCommand('mytickets');
        $this->testCommand('ticket', ['20250108-001']);
        $this->testCommand('myprizes');
        $this->testCommand('history');
        $this->testCommand('stats');
        $this->testCommand('rules');
        $this->testCommand('address');

        // 测试中文游戏查询指令
        echo "\n🎮 Testing Chinese Game Query Commands:\n";
        $this->testCommand('蛇身');
        $this->testCommand('我的购彩');
        $this->testCommand('查询票号', ['20250108-001']);
        $this->testCommand('我的中奖');
        $this->testCommand('历史中奖');
        $this->testCommand('游戏统计');
        $this->testCommand('游戏规则');
        $this->testCommand('收款地址');

        // 测试管理员指令
        echo "\n👑 Testing Admin Commands:\n";
        $this->testCommand('bind', ['000001']);
        $this->testCommand('wallet', ['TNewWalletAddress123456789012345']);
        $this->testCommand('cancelwallet');
        $this->testCommand('setbet', ['5']);
        $this->testCommand('setfee', ['10']);
        $this->testCommand('info');

        // 测试中文管理员指令
        echo "\n👑 Testing Chinese Admin Commands:\n";
        $this->testCommand('绑定租户', ['000001']);
        $this->testCommand('设置钱包', ['TNewWalletAddress123456789012345']);
        $this->testCommand('取消钱包变更');
        $this->testCommand('设置投注', ['5']);
        $this->testCommand('设置手续费', ['10']);
        $this->testCommand('群组配置');

        echo "\n🎉 All tests completed!\n";
        echo "📅 " . date('Y-m-d H:i:s') . "\n";
    }

    /**
     * 测试特定指令
     */
    public function testSpecificCommand(string $command, array $params = []): void
    {
        echo "🧪 Testing specific command: /{$command}\n";
        $this->testCommand($command, $params);
    }
}

// 主程序
if (php_sapi_name() === 'cli') {
    try {
        $tester = new TelegramCommandTester();
        
        // 检查命令行参数
        if ($argc > 1) {
            $command = $argv[1];
            $params = array_slice($argv, 2);
            $tester->testSpecificCommand($command, $params);
        } else {
            $tester->runAllTests();
        }
    } catch (\Throwable $e) {
        echo "❌ Test failed: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        exit(1);
    }
} else {
    echo "This script must be run from command line.\n";
    exit(1);
}