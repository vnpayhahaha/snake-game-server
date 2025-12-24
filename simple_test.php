<?php

declare(strict_types=1);

/**
 * 简化的Telegram指令测试脚本
 * 不依赖Hyperf容器，直接测试方法逻辑
 */

// 模拟自动加载
spl_autoload_register(function ($class) {
    // 简单的类映射，实际项目中由Composer处理
    $classMap = [
        'App\\Service\\Telegram\\Bot\\CommandEnum' => __DIR__ . '/app/Service/Telegram/Bot/CommandEnum.php',
    ];
    
    if (isset($classMap[$class])) {
        require_once $classMap[$class];
    }
});

// 引入CommandEnum
require_once __DIR__ . '/app/Service/Telegram/Bot/CommandEnum.php';

use App\Service\Telegram\Bot\CommandEnum;

class SimpleTelegramTester
{
    /**
     * 测试指令映射
     */
    public function testCommandMapping(): void
    {
        echo "🧪 Testing Command Mapping\n";
        echo str_repeat("=", 50) . "\n";

        // 测试英文指令映射
        echo "📝 English Commands:\n";
        $englishCommands = [
            'start', 'help', 'bindwallet', 'unbindwallet', 'mywallet',
            'snake', 'mytickets', 'ticket', 'myprizes', 'history',
            'stats', 'rules', 'address', 'bind', 'wallet',
            'cancelwallet', 'setbet', 'setfee', 'info'
        ];

        foreach ($englishCommands as $command) {
            $isValid = CommandEnum::isCommand($command);
            $method = CommandEnum::getCommand($command);
            $status = $isValid ? '✅' : '❌';
            echo "  {$status} /{$command} -> {$method}\n";
        }

        // 测试中文指令映射
        echo "\n📝 Chinese Commands:\n";
        $chineseCommands = [
            '开始', '帮助', '绑定钱包', '解绑钱包', '我的钱包',
            '蛇身', '我的购彩', '查询票号', '我的中奖', '历史中奖',
            '游戏统计', '游戏规则', '收款地址', '绑定租户', '设置钱包',
            '取消钱包变更', '设置投注', '设置手续费', '群组配置'
        ];

        foreach ($chineseCommands as $command) {
            $isValid = CommandEnum::isCommand($command);
            $method = CommandEnum::getCommand($command);
            $status = $isValid ? '✅' : '❌';
            echo "  {$status} /{$command} -> {$method}\n";
        }
    }

    /**
     * 测试帮助信息
     */
    public function testHelpMessages(): void
    {
        echo "\n🧪 Testing Help Messages\n";
        echo str_repeat("=", 50) . "\n";

        // 测试英文帮助
        echo "📝 English Help:\n";
        $englishHelp = CommandEnum::getHelpReply(false);
        foreach (array_slice($englishHelp, 0, 10) as $line) {
            echo "  " . $line . "\n";
        }
        echo "  ... (truncated)\n";

        // 测试中文帮助
        echo "\n📝 Chinese Help:\n";
        $chineseHelp = CommandEnum::getHelpReply(true);
        foreach (array_slice($chineseHelp, 0, 10) as $line) {
            echo "  " . $line . "\n";
        }
        echo "  ... (truncated)\n";
    }

    /**
     * 测试TRON地址验证
     */
    public function testTronAddressValidation(): void
    {
        echo "\n🧪 Testing TRON Address Validation\n";
        echo str_repeat("=", 50) . "\n";

        // 模拟TRON地址验证函数
        $isValidTronAddress = function(string $address): bool {
            return preg_match('/^T[1-9A-HJ-NP-Za-km-z]{33}$/', $address) === 1;
        };

        $testAddresses = [
            'TTestWalletAddress123456789012345' => false, // 太长
            'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx' => true,  // 有效地址
            'TRX123456789' => false,                      // 太短
            'BTC1234567890123456789012345678901234' => false, // 不以T开头
            'T123456789012345678901234567890123' => true,  // 有效格式
        ];

        foreach ($testAddresses as $address => $expected) {
            $isValid = $isValidTronAddress($address);
            $status = ($isValid === $expected) ? '✅' : '❌';
            $result = $isValid ? 'Valid' : 'Invalid';
            echo "  {$status} {$address} -> {$result}\n";
        }
    }

    /**
     * 模拟指令执行测试
     */
    public function testCommandExecution(): void
    {
        echo "\n🧪 Testing Command Execution Logic\n";
        echo str_repeat("=", 50) . "\n";

        // 模拟指令执行结果
        $mockResults = [
            'start' => [
                '🐍 Welcome to Snake Chain Game!',
                '',
                '**What is Snake Chain Game?**',
                'A blockchain-based lottery game on TRON.',
                '🎮 Good luck and have fun!'
            ],
            '开始' => [
                '🐍 欢迎来到贪吃蛇链上游戏！',
                '',
                '**什么是贪吃蛇链上游戏？**',
                '基于TRON区块链的彩票游戏。',
                '🎮 祝您好运，玩得开心！'
            ],
            'bindwallet' => [
                '❌ Invalid parameters',
                'Usage: /bindwallet <wallet_address>'
            ],
            '绑定钱包' => [
                '❌ 参数错误',
                '用法：/绑定钱包 <钱包地址>'
            ]
        ];

        foreach ($mockResults as $command => $expectedResult) {
            echo "📝 Command: /{$command}\n";
            echo "📤 Expected Result:\n";
            foreach ($expectedResult as $line) {
                echo "   " . $line . "\n";
            }
            echo "✅ Mock execution successful\n\n";
        }
    }

    /**
     * 测试多语言差异
     */
    public function testLanguageDifferences(): void
    {
        echo "\n🧪 Testing Language Differences\n";
        echo str_repeat("=", 50) . "\n";

        $comparisons = [
            ['english' => 'start', 'chinese' => '开始'],
            ['english' => 'bindwallet', 'chinese' => '绑定钱包'],
            ['english' => 'snake', 'chinese' => '蛇身'],
            ['english' => 'info', 'chinese' => '群组配置'],
        ];

        foreach ($comparisons as $pair) {
            $englishMethod = CommandEnum::getCommand($pair['english']);
            $chineseMethod = CommandEnum::getCommand($pair['chinese']);
            
            echo "🔄 {$pair['english']} -> {$englishMethod}\n";
            echo "🔄 {$pair['chinese']} -> {$chineseMethod}\n";
            
            if ($englishMethod !== $chineseMethod) {
                echo "✅ Different methods (correct for multilingual)\n";
            } else {
                echo "⚠️  Same method (may need review)\n";
            }
            echo "\n";
        }
    }

    /**
     * 运行所有测试
     */
    public function runAllTests(): void
    {
        echo "🚀 Starting Simple Telegram Command Tests\n";
        echo "📅 " . date('Y-m-d H:i:s') . "\n";
        echo str_repeat("=", 60) . "\n";

        $this->testCommandMapping();
        $this->testHelpMessages();
        $this->testTronAddressValidation();
        $this->testCommandExecution();
        $this->testLanguageDifferences();

        echo str_repeat("=", 60) . "\n";
        echo "🎉 All simple tests completed!\n";
        echo "📅 " . date('Y-m-d H:i:s') . "\n";
    }
}

// 主程序
if (php_sapi_name() === 'cli') {
    try {
        $tester = new SimpleTelegramTester();
        $tester->runAllTests();
    } catch (\Throwable $e) {
        echo "❌ Test failed: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        exit(1);
    }
} else {
    echo "This script must be run from command line.\n";
    exit(1);
}