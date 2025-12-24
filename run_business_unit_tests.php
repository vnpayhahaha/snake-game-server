<?php

declare(strict_types=1);

/**
 * 业务单元测试运行器 - 专门测试 tests/Unit 下的业务功能
 * 根据错误信息定位问题并提供修复建议
 */

use Hyperf\Contract\ApplicationInterface;
use Hyperf\Di\ClassLoader;
use Mine\AppStore\Plugin;

// 设置错误报告
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

// 定义常量
! defined('BASE_PATH') && define('BASE_PATH', __DIR__);
! defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);
! defined('START_TIME') && define('START_TIME', time());
! defined('HF_VERSION') && define('HF_VERSION', '3.1');

echo "🧪 Business Unit Tests Runner\n";
echo "📅 " . date('Y-m-d H:i:s') . "\n";
echo "🎯 Testing: tests/Unit/ business functionality\n";
echo str_repeat('=', 60) . "\n";

try {
    // 加载依赖
    require BASE_PATH . '/vendor/autoload.php';
    
    Plugin::init();
    ClassLoader::init();
    
    $container = require BASE_PATH . '/config/container.php';
    $container->get(ApplicationInterface::class);
    
    echo "✅ Hyperf framework initialized successfully\n\n";
    
} catch (Throwable $e) {
    echo "❌ Failed to initialize Hyperf framework:\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n🔧 Possible fixes:\n";
    echo "   1. Check if all dependencies are installed: composer install\n";
    echo "   2. Check database configuration in .env\n";
    echo "   3. Ensure Redis service is running\n";
    exit(1);
}

// 运行业务单元测试
runBusinessUnitTests();

function runBusinessUnitTests(): void
{
    echo "📋 Running Business Unit Tests:\n";
    echo str_repeat('-', 40) . "\n";
    
    $testResults = [];
    
    // 测试 CommandEnum 业务逻辑
    $testResults['CommandEnum'] = testCommandEnumBusiness();
    
    // 测试 TelegramCommandService 业务逻辑
    $testResults['TelegramCommandService'] = testTelegramCommandServiceBusiness();
    
    // 汇总测试结果
    summarizeTestResults($testResults);
}

function testCommandEnumBusiness(): array
{
    echo "\n🧪 Testing CommandEnum Business Logic...\n";
    $results = ['passed' => 0, 'failed' => 0, 'errors' => []];
    
    try {
        // 测试1: 指令映射完整性（业务需求）
        echo "  📝 Test 1: Command mapping completeness\n";
        $englishCommands = \App\Service\Telegram\Bot\CommandEnum::COMMAND_SET;
        $chineseCommands = \App\Service\Telegram\Bot\CommandEnum::COMMAND_SET_CN;
        
        if (count($englishCommands) !== count($chineseCommands)) {
            throw new Exception("English and Chinese command counts don't match: " . 
                count($englishCommands) . " vs " . count($chineseCommands));
        }
        
        // 验证关键业务指令存在
        $requiredCommands = [
            'start', 'help', 'bindwallet', 'unbindwallet', 'mywallet',
            'snake', 'mytickets', 'ticket', 'myprizes', 'history',
            'stats', 'rules', 'address', 'bind', 'wallet', 'info'
        ];
        
        foreach ($requiredCommands as $cmd) {
            if (!array_key_exists($cmd, $englishCommands)) {
                throw new Exception("Required English command missing: {$cmd}");
            }
        }
        
        $requiredChineseCommands = [
            '开始', '帮助', '绑定钱包', '解绑钱包', '我的钱包',
            '蛇身', '我的购彩', '查询票号', '我的中奖', '历史中奖',
            '游戏统计', '游戏规则', '收款地址', '绑定租户', '设置钱包', '群组配置'
        ];
        
        foreach ($requiredChineseCommands as $cmd) {
            if (!array_key_exists($cmd, $chineseCommands)) {
                throw new Exception("Required Chinese command missing: {$cmd}");
            }
        }
        
        echo "    ✅ All required commands present\n";
        $results['passed']++;
        
        // 测试2: 大小写不敏感（用户体验需求）
        echo "  📝 Test 2: Case insensitive command recognition\n";
        $testCases = [
            ['start', 'Start'],
            ['START', 'Start'],
            ['help', 'Help'],
            ['HELP', 'Help'],
            ['bindwallet', 'BindWallet'],
            ['BINDWALLET', 'BindWallet'],
        ];
        
        foreach ($testCases as [$input, $expected]) {
            if (!\App\Service\Telegram\Bot\CommandEnum::isCommand($input)) {
                throw new Exception("Case insensitive command '{$input}' not recognized");
            }
            
            $method = \App\Service\Telegram\Bot\CommandEnum::getCommand($input);
            if ($method !== $expected) {
                throw new Exception("Case insensitive command '{$input}' mapped to '{$method}', expected '{$expected}'");
            }
        }
        
        echo "    ✅ Case insensitive recognition working\n";
        $results['passed']++;
        
        // 测试3: 帮助信息业务逻辑
        echo "  📝 Test 3: Help message business logic\n";
        $englishHelp = \App\Service\Telegram\Bot\CommandEnum::getHelpReply(false);
        $chineseHelp = \App\Service\Telegram\Bot\CommandEnum::getHelpReply(true);
        
        // 验证帮助信息包含关键业务信息
        $englishHelpText = implode(' ', $englishHelp);
        if (!str_contains($englishHelpText, 'Snake Chain Game')) {
            throw new Exception("English help missing game title");
        }
        if (!str_contains($englishHelpText, 'TRON')) {
            throw new Exception("English help missing TRON reference");
        }
        
        $chineseHelpText = implode(' ', $chineseHelp);
        if (!str_contains($chineseHelpText, '贪吃蛇链上游戏') && !str_contains($chineseHelpText, 'Snake Chain Game')) {
            throw new Exception("Chinese help missing game title");
        }
        if (!str_contains($chineseHelpText, 'TRON')) {
            throw new Exception("Chinese help missing TRON reference");
        }
        
        echo "    ✅ Help messages contain required business information\n";
        $results['passed']++;
        
        // 测试4: 队列名称常量（异步处理需求）
        echo "  📝 Test 4: Queue name constants for async processing\n";
        $requiredQueues = [
            'TELEGRAM_COMMAND_RUN_QUEUE_NAME',
            'TELEGRAM_NOTICE_QUEUE_NAME', 
            'TRON_TX_PROCESS_QUEUE_NAME',
            'PRIZE_DISPATCH_QUEUE_NAME'
        ];
        
        foreach ($requiredQueues as $queueConst) {
            if (!defined("App\\Service\\Telegram\\Bot\\CommandEnum::{$queueConst}")) {
                throw new Exception("Queue constant missing: {$queueConst}");
            }
        }
        
        echo "    ✅ All required queue constants defined\n";
        $results['passed']++;
        
        echo "✅ CommandEnum business tests completed successfully\n";
        
    } catch (Throwable $e) {
        $results['failed']++;
        $results['errors'][] = [
            'test' => 'CommandEnum Business Logic',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'suggestions' => [
                'Check CommandEnum::COMMAND_SET and COMMAND_SET_CN arrays',
                'Verify isCommand() and getCommand() methods handle case insensitivity',
                'Ensure getHelpReply() generates proper business content',
                'Confirm all queue constants are defined'
            ]
        ];
        
        echo "❌ CommandEnum business test failed:\n";
        echo "   Error: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
    
    return $results;
}

function testTelegramCommandServiceBusiness(): array
{
    echo "\n🧪 Testing TelegramCommandService Business Logic...\n";
    $results = ['passed' => 0, 'failed' => 0, 'errors' => []];
    
    try {
        // 创建模拟环境
        $mockBot = createMockTelegramBot();
        $container = \Hyperf\Context\ApplicationContext::getContainer();
        $commandService = $container->get(\App\Service\Telegram\Bot\TelegramCommandService::class);
        $commandService->setTelegramBot($mockBot);
        
        echo "  📝 Test 1: Service initialization and dependency injection\n";
        if (!$commandService instanceof \App\Service\Telegram\Bot\TelegramCommandService) {
            throw new Exception("TelegramCommandService not properly instantiated");
        }
        echo "    ✅ Service properly initialized\n";
        $results['passed']++;
        
        // 测试2: 基础业务指令功能
        echo "  📝 Test 2: Core business command functionality\n";
        $coreCommands = [
            ['Start', '123456789', [], 'Welcome to Snake Chain Game'],
            ['cnStart', '123456789', [], '欢迎来到贪吃蛇链上游戏'],
            ['Help', '123456789', [], 'Command List'],
            ['cnHelp', '123456789', [], '指令列表'],
            ['Rules', '123456789', [], 'Game Rules'],
            ['cnRules', '123456789', [], '游戏规则'],
        ];
        
        foreach ($coreCommands as [$method, $userId, $params, $expectedContent]) {
            if (!method_exists($commandService, $method)) {
                throw new Exception("Core business method missing: {$method}");
            }
            
            $result = $commandService->{$method}((int)$userId, $params, 1);
            
            if (!is_array($result) || empty($result)) {
                throw new Exception("Method {$method} should return non-empty array");
            }
            
            $found = false;
            foreach ($result as $line) {
                if (str_contains($line, $expectedContent)) {
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                throw new Exception("Method {$method} result missing expected content: {$expectedContent}");
            }
        }
        
        echo "    ✅ Core business commands working correctly\n";
        $results['passed']++;
        
        // 测试3: 钱包业务逻辑
        echo "  📝 Test 3: Wallet business logic validation\n";
        
        // 测试无效钱包地址
        $walletTests = [
            ['BindWallet', [], 'Invalid parameters'],
            ['BindWallet', ['invalid_address'], 'Invalid TRON wallet address'],
            ['cnBindWallet', [], '参数错误'],
            ['cnBindWallet', ['invalid_address'], '无效的TRON钱包地址'],
        ];
        
        foreach ($walletTests as [$method, $params, $expectedError]) {
            $result = $commandService->{$method}(123456789, $params, 1);
            
            if (!is_array($result)) {
                throw new Exception("Wallet method {$method} should return array for validation");
            }
            
            $found = false;
            foreach ($result as $line) {
                if (str_contains($line, $expectedError)) {
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                throw new Exception("Wallet method {$method} missing expected error: {$expectedError}");
            }
        }
        
        echo "    ✅ Wallet validation logic working correctly\n";
        $results['passed']++;
        
        // 测试4: 方法签名业务规范
        echo "  📝 Test 4: Business method signature compliance\n";
        $reflection = new ReflectionClass($commandService);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        
        $commandMethods = array_filter($methods, function($method) {
            $name = $method->getName();
            return (
                in_array($name, array_values(\App\Service\Telegram\Bot\CommandEnum::COMMAND_SET)) ||
                in_array($name, array_values(\App\Service\Telegram\Bot\CommandEnum::COMMAND_SET_CN))
            ) && !in_array($name, ['__construct', 'setTelegramBot']);
        });
        
        if (count($commandMethods) < 10) {
            throw new Exception("Too few command methods found: " . count($commandMethods));
        }
        
        foreach ($commandMethods as $method) {
            $parameters = $method->getParameters();
            
            if (count($parameters) !== 3) {
                throw new Exception("Method {$method->getName()} has wrong parameter count: " . count($parameters));
            }
            
            $paramNames = array_map(fn($p) => $p->getName(), $parameters);
            $expectedParams = ['userId', 'params', 'recordID'];
            
            if ($paramNames !== $expectedParams) {
                throw new Exception("Method {$method->getName()} has wrong parameter names: " . implode(', ', $paramNames));
            }
        }
        
        echo "    ✅ All command methods follow business signature standard\n";
        $results['passed']++;
        
        echo "✅ TelegramCommandService business tests completed successfully\n";
        
    } catch (Throwable $e) {
        $results['failed']++;
        $results['errors'][] = [
            'test' => 'TelegramCommandService Business Logic',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'suggestions' => [
                'Check if TelegramCommandService is properly registered in DI container',
                'Verify all command methods exist and return proper array responses',
                'Ensure wallet validation logic is implemented correctly',
                'Confirm method signatures follow (int $userId, array $params, int $recordID): array pattern'
            ]
        ];
        
        echo "❌ TelegramCommandService business test failed:\n";
        echo "   Error: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
    
    return $results;
}

function createMockTelegramBot()
{
    return new class {
        public function ChatID(): string { return '-1001234567890'; }
        public function UserId(): string { return '123456789'; }
        public function UserName(): ?string { return 'testuser'; }
        public function FirstName(): string { return 'Test'; }
        public function LastName(): string { return 'User'; }
        public function MessageID(): string { return '12345'; }
        public function getGroupTitle(): string { return 'Test Snake Game Group'; }
        public function getChatMember(array $params): array {
            return [
                'ok' => true,
                'result' => ['status' => 'administrator']
            ];
        }
    };
}

function summarizeTestResults(array $testResults): void
{
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "📊 Business Unit Test Summary\n";
    echo str_repeat('-', 30) . "\n";
    
    $totalPassed = 0;
    $totalFailed = 0;
    $allErrors = [];
    
    foreach ($testResults as $testName => $result) {
        $totalPassed += $result['passed'];
        $totalFailed += $result['failed'];
        $allErrors = array_merge($allErrors, $result['errors']);
        
        $status = $result['failed'] > 0 ? '❌' : '✅';
        echo "{$status} {$testName}: {$result['passed']} passed, {$result['failed']} failed\n";
    }
    
    echo str_repeat('-', 30) . "\n";
    echo "🎯 Total: {$totalPassed} passed, {$totalFailed} failed\n";
    
    if ($totalFailed > 0) {
        echo "\n🔧 Issues Found and Suggested Fixes:\n";
        echo str_repeat('-', 40) . "\n";
        
        foreach ($allErrors as $i => $error) {
            echo "\n" . ($i + 1) . ". {$error['test']}\n";
            echo "   ❌ Error: {$error['error']}\n";
            echo "   📁 File: {$error['file']}:{$error['line']}\n";
            echo "   🔧 Suggestions:\n";
            foreach ($error['suggestions'] as $suggestion) {
                echo "      • {$suggestion}\n";
            }
        }
        
        echo "\n📋 Next Steps:\n";
        echo "1. Fix the issues listed above\n";
        echo "2. Re-run this test: php run_business_unit_tests.php\n";
        echo "3. Run full integration tests once unit tests pass\n";
        
    } else {
        echo "\n🎉 All business unit tests passed!\n";
        echo "✅ Core business logic is working correctly\n";
        echo "✅ Command mapping and validation are functional\n";
        echo "✅ Method signatures follow business standards\n";
        
        echo "\n📋 Ready for next steps:\n";
        echo "1. Run integration tests: php bin/hyperf.php telegram:test\n";
        echo "2. Test with real Telegram bot integration\n";
        echo "3. Deploy to staging environment\n";
    }
    
    echo "\n📅 Test completed: " . date('Y-m-d H:i:s') . "\n";
}