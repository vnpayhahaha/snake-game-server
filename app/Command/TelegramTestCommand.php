<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Telegram\Bot\TelegramCommandService;
use App\Service\Telegram\Bot\CommandEnum;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class TelegramTestCommand extends HyperfCommand
{
    #[Inject]
    protected ContainerInterface $container;

    #[Inject]
    protected StdoutLoggerInterface $logger;

    #[Inject]
    protected TelegramCommandService $telegramCommandService;

    public function __construct()
    {
        parent::__construct('telegram:test');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Test Telegram bot commands');
        $this->addArgument('test-command', InputArgument::OPTIONAL, 'Specific command to test', 'all');
        $this->addOption('params', 'p', InputOption::VALUE_OPTIONAL, 'Command parameters (JSON format)', '[]');
        $this->addOption('user-id', 'u', InputOption::VALUE_OPTIONAL, 'Test user ID', '123456789');
        $this->addOption('lang', 'l', InputOption::VALUE_OPTIONAL, 'Language (en/cn)', 'both');
    }

    public function handle()
    {
        $this->info('🚀 Starting Telegram Command Tests');
        $this->info('📅 ' . date('Y-m-d H:i:s'));
        $this->line(str_repeat('=', 60));

        // 设置模拟的Telegram Bot
        $this->setupMockTelegramBot();

        $command = $this->input->getArgument('test-command');
        $params = json_decode($this->input->getOption('params'), true) ?? [];
        $userId = (int)$this->input->getOption('user-id');
        $lang = $this->input->getOption('lang');

        if ($command === 'all') {
            $this->runAllTests($lang);
        } else {
            $this->testSpecificCommand($command, $params, $userId);
        }

        $this->line(str_repeat('=', 60));
        $this->info('🎉 Tests completed!');
        $this->info('📅 ' . date('Y-m-d H:i:s'));

        return 0;
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

        $this->telegramCommandService->setTelegramBot($mockBot);
    }

    /**
     * 运行所有测试
     */
    private function runAllTests(string $lang): void
    {
        if ($lang === 'both' || $lang === 'en') {
            $this->info('📋 Testing English Commands:');
            $this->testEnglishCommands();
        }

        if ($lang === 'both' || $lang === 'cn') {
            $this->info('📋 Testing Chinese Commands:');
            $this->testChineseCommands();
        }

        $this->testCommandMapping();
        $this->testHelpMessages();
    }

    /**
     * 测试英文指令
     */
    private function testEnglishCommands(): void
    {
        $commands = [
            // 基础指令
            ['start', []],
            ['help', []],
            
            // 钱包指令
            ['bindwallet', ['TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx']],
            ['mywallet', []],
            ['unbindwallet', []],
            
            // 游戏查询指令
            ['snake', []],
            ['mytickets', []],
            ['ticket', ['20250108-001']],
            ['myprizes', []],
            ['history', []],
            ['stats', []],
            ['rules', []],
            ['address', []],
            
            // 管理员指令
            ['bind', ['000001']],
            ['wallet', ['TNewWalletAddress123456789012345']],
            ['cancelwallet', []],
            ['setbet', ['5']],
            ['setfee', ['10']],
            ['info', []],
        ];

        foreach ($commands as [$command, $params]) {
            $this->testCommand($command, $params, 123456789);
        }
    }

    /**
     * 测试中文指令
     */
    private function testChineseCommands(): void
    {
        $commands = [
            // 基础指令
            ['开始', []],
            ['帮助', []],
            
            // 钱包指令
            ['绑定钱包', ['TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx']],
            ['我的钱包', []],
            ['解绑钱包', []],
            
            // 游戏查询指令
            ['蛇身', []],
            ['我的购彩', []],
            ['查询票号', ['20250108-001']],
            ['我的中奖', []],
            ['历史中奖', []],
            ['游戏统计', []],
            ['游戏规则', []],
            ['收款地址', []],
            
            // 管理员指令
            ['绑定租户', ['000001']],
            ['设置钱包', ['TNewWalletAddress123456789012345']],
            ['取消钱包变更', []],
            ['设置投注', ['5']],
            ['设置手续费', ['10']],
            ['群组配置', []],
        ];

        foreach ($commands as [$command, $params]) {
            $this->testCommand($command, $params, 123456789);
        }
    }

    /**
     * 测试特定指令
     */
    private function testSpecificCommand(string $command, array $params, int $userId): void
    {
        $this->info("🧪 Testing specific command: /{$command}");
        $this->testCommand($command, $params, $userId);
    }

    /**
     * 测试单个指令
     */
    private function testCommand(string $command, array $params, int $userId): void
    {
        $this->line('');
        $this->comment("Testing: /{$command} " . implode(' ', $params));
        $this->line(str_repeat('-', 40));

        try {
            // 检查指令是否有效
            if (!CommandEnum::isCommand($command)) {
                $this->error("❌ Invalid command: {$command}");
                return;
            }

            // 获取方法名
            $method = CommandEnum::getCommand($command);
            $this->line("🎯 Method: {$method}");

            // 检查方法是否存在
            if (!method_exists($this->telegramCommandService, $method)) {
                $this->error("❌ Method not found: {$method}");
                return;
            }

            // 执行指令
            $startTime = microtime(true);
            $result = $this->telegramCommandService->{$method}($userId, $params, 1);
            $endTime = microtime(true);

            // 显示结果
            $this->line("⏱️  Execution time: " . round(($endTime - $startTime) * 1000, 2) . "ms");
            $this->line("📤 Result:");
            
            if (is_array($result)) {
                foreach ($result as $line) {
                    $this->line("   " . $line);
                }
            } else {
                $this->line("   " . $result);
            }

            $this->info("✅ Command executed successfully");

        } catch (\Throwable $e) {
            $this->error("❌ Command execution failed:");
            $this->error("   Error: " . $e->getMessage());
            $this->error("   File: " . $e->getFile() . ":" . $e->getLine());
            
            // 显示堆栈跟踪（仅前3行）
            $trace = explode("\n", $e->getTraceAsString());
            $this->line("   Stack trace:");
            for ($i = 0; $i < min(3, count($trace)); $i++) {
                $this->line("     " . $trace[$i]);
            }
        }
    }

    /**
     * 测试指令映射
     */
    private function testCommandMapping(): void
    {
        $this->info('🧪 Testing Command Mapping:');
        
        // 测试英文指令
        $englishCommands = ['start', 'help', 'bindwallet', 'snake', 'info'];
        foreach ($englishCommands as $command) {
            $isValid = CommandEnum::isCommand($command);
            $method = CommandEnum::getCommand($command);
            $status = $isValid ? '✅' : '❌';
            $this->line("  {$status} /{$command} -> {$method}");
        }

        // 测试中文指令
        $chineseCommands = ['开始', '帮助', '绑定钱包', '蛇身', '群组配置'];
        foreach ($chineseCommands as $command) {
            $isValid = CommandEnum::isCommand($command);
            $method = CommandEnum::getCommand($command);
            $status = $isValid ? '✅' : '❌';
            $this->line("  {$status} /{$command} -> {$method}");
        }
    }

    /**
     * 测试帮助信息
     */
    private function testHelpMessages(): void
    {
        $this->info('🧪 Testing Help Messages:');
        
        try {
            $englishHelp = CommandEnum::getHelpReply(false);
            $chineseHelp = CommandEnum::getHelpReply(true);
            
            $this->line("✅ English help: " . count($englishHelp) . " lines");
            $this->line("✅ Chinese help: " . count($chineseHelp) . " lines");
            
            // 显示前几行作为示例
            $this->line("📝 English help preview:");
            foreach (array_slice($englishHelp, 0, 3) as $line) {
                $this->line("   " . $line);
            }
            
            $this->line("📝 Chinese help preview:");
            foreach (array_slice($chineseHelp, 0, 3) as $line) {
                $this->line("   " . $line);
            }
            
        } catch (\Throwable $e) {
            $this->error("❌ Help message test failed: " . $e->getMessage());
        }
    }
}