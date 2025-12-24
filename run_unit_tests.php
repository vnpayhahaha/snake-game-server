<?php

declare(strict_types=1);

/**
 * 简化的单元测试运行器 - 专门用于Docker容器环境
 * 只测试 tests/Unit 下的业务相关测试
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

echo "🚀 Starting Unit Tests for Snake Game Telegram Bot\n";
echo "📅 " . date('Y-m-d H:i:s') . "\n";
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
    exit(1);
}

// 运行测试
runUnitTests();

function runUnitTests(): void
{
    echo "📋 Running Unit Tests:\n";
    echo str_repeat('-', 40) . "\n";
    
    // 测试 CommandEnum
    testCommandEnum();
    
    // 测试 TelegramCommandService
    testTelegramCommandService();
    
    echo str_repeat('=', 60) . "\n";
    echo "🎉 All Unit Tests Completed!\n";
    echo "📅 " . date('Y-m-d H:i:s') . "\n";
}

function testCommandEnum(): void
{
    echo "\n🧪 Testing CommandEnum...\n";
    
    try {
        // 测试英文指令映射
        $englishCommands = [
            'start' => 'Start',
            'help' => 'Help',
            'bindwallet' => 'BindWallet',
            'snake' => 'Snake',
            'info' => 'Info',
        ];
        
        foreach ($englishCommands as $command => $expectedMethod) {
            $isValid = \App\Service\Telegram\Bot\CommandEnum::isCommand($command);
            $method = \App\Service\Telegram\Bot\CommandEnum::getCommand($command);
            
            if (!$isValid) {
                throw new Exception("Command '{$command}' should be valid");
            }
            
            if ($method !== $expectedMethod) {
                throw new Exception("Command '{$command}' should map to '{$expectedMethod}', got '{$method}'");
            }
        }
        echo "  ✅ English commands mapping test passed\n";
        
        // 测试中文指令映射
        $chineseCommands = [
            '开始' => 'cnStart',
            '帮助' => 'cnHelp',
            '绑定钱包' => 'cnBindWallet',
            '蛇身' => 'cnSnake',
            '群组配置' => 'cnInfo',
        ];
        
        foreach ($chineseCommands as $command => $expectedMethod) {
            $isValid = \App\Service\Telegram\Bot\CommandEnum::isCommand($command);
            $method = \App\Service\Telegram\Bot\CommandEnum::getCommand($command);
            
            if (!$isValid) {
                throw new Exception("Chinese command '{$command}' should be valid");
            }
            
            if ($method !== $expectedMethod) {
                throw new Exception("Chinese command '{$command}' should map to '{$expectedMethod}', got '{$method}'");
            }
        }
        echo "  ✅ Chinese commands mapping test passed\n";
        
        // 测试大小写不敏感
        $caseTests = [
            'START' => 'Start',
            'start' => 'Start',
            'Help' => 'Help',
            'HELP' => 'Help',
            'bindwallet' => 'BindWallet',
            'BINDWALLET' => 'BindWallet',
        ];
        
        foreach ($caseTests as $command => $expectedMethod) {
            $isValid = \App\Service\Telegram\Bot\CommandEnum::isCommand($command);
            $method = \App\Service\Telegram\Bot\CommandEnum::getCommand($command);
            
            if (!$isValid) {
                throw new Exception("Case insensitive command '{$command}' should be valid");
            }
            
            if ($method !== $expectedMethod) {
                throw new Exception("Case insensitive command '{$command}' should map to '{$expectedMethod}', got '{$method}'");
            }
        }
        echo "  ✅ Case insensitive test passed\n";
        
        // 测试无效指令
        $invalidCommands = ['invalid', 'notexist', '无效指令', '', ' '];
        foreach ($invalidCommands as $command) {
            $isValid = \App\Service\Telegram\Bot\CommandEnum::isCommand($command);
            $method = \App\Service\Telegram\Bot\CommandEnum::getCommand($command);
            
            if ($isValid) {
                throw new Exception("Invalid command '{$command}' should be rejected");
            }
            
            if ($method !== '') {
                throw new Exception("Invalid command '{$command}' should return empty method");
            }
        }
        echo "  ✅ Invalid commands rejection test passed\n";
        
        // 测试帮助信息
        $englishHelp = \App\Service\Telegram\Bot\CommandEnum::getHelpReply(false);
        $chineseHelp = \App\Service\Telegram\Bot\CommandEnum::getHelpReply(true);
        
        if (!is_array($englishHelp) || empty($englishHelp)) {
            throw new Exception("English help should be non-empty array");
        }
        
        if (!is_array($chineseHelp) || empty($chineseHelp)) {
            throw new Exception("Chinese help should be non-empty array");
        }
        
        if (!str_contains($englishHelp[0], 'Snake Chain Game Command List')) {
            throw new Exception("English help should contain proper title");
        }
        
        if (!str_contains($chineseHelp[0], 'Snake Chain Game 指令列表')) {
            throw new Exception("Chinese help should contain proper title");
        }
        
        echo "  ✅ Help messages test passed\n";
        
        echo "✅ CommandEnum tests completed successfully\n";
        
    } catch (Throwable $e) {
        echo "❌ CommandEnum test failed:\n";
        echo "   Error: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

function testTelegramCommandService(): void
{
    echo "\n🧪 Testing TelegramCommandService...\n";
    
    try {
        // 创建模拟的Telegram Bot
        $mockBot = new class {
            public function ChatID(): string { return '-1001234567890'; }
            public function UserId(): string { return '123456789'; }
            public function UserName(): ?string { return 'testuser'; }
            public function FirstName(): string { return 'Test'; }
            public function LastName(): string { return 'User'; }
            public function MessageID(): string { return '12345'; }
            public function getGroupTitle(): string { return 'Test Group'; }
            public function getChatMember(array $params): array {
                return [
                    'ok' => true,
                    'result' => ['status' => 'administrator']
                ];
            }
        };
        
        // 获取服务实例
        $container = \Hyperf\Context\ApplicationContext::getContainer();
        $commandService = $container->get(\App\Service\Telegram\Bot\TelegramCommandService::class);
        $commandService->setTelegramBot($mockBot);
        
        echo "  ✅ TelegramCommandService instance created\n";
        
        // 测试基础指令
        $basicTests = [
            ['Start', 'Welcome to Snake Chain Game!'],
            ['cnStart', '欢迎来到贪吃蛇链上游戏！'],
            ['Help', 'Snake Chain Game Command List'],
            ['cnHelp', 'Snake Chain Game 指令列表'],
            ['Rules', 'Snake Chain Game Rules'],
            ['cnRules', '贪吃蛇链上游戏规则'],
        ];
        
        foreach ($basicTests as [$method, $expectedContent]) {
            if (!method_exists($commandService, $method)) {
                throw new Exception("Method '{$method}' should exist in TelegramCommandService");
            }
            
            $result = $commandService->{$method}(123456789, [], 1);
            
            if (!is_array($result)) {
                throw new Exception("Method '{$method}' should return array");
            }
            
            if (empty($result)) {
                throw new Exception("Method '{$method}' should return non-empty array");
            }
            
            $found = false;
            foreach ($result as $line) {
                if (str_contains($line, $expectedContent)) {
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                throw new Exception("Method '{$method}' result should contain '{$expectedContent}'");
            }
        }
        echo "  ✅ Basic commands test passed\n";
        
        // 测试参数验证
        $paramTests = [
            ['BindWallet', [], 'Invalid parameters'],
            ['BindWallet', ['invalid_address'], 'Invalid TRON wallet address'],
            ['cnBindWallet', [], '参数错误'],
            ['cnBindWallet', ['invalid_address'], '无效的TRON钱包地址'],
        ];
        
        foreach ($paramTests as [$method, $params, $expectedError]) {
            $result = $commandService->{$method}(123456789, $params, 1);
            
            if (!is_array($result)) {
                throw new Exception("Method '{$method}' should return array for invalid params");
            }
            
            $found = false;
            foreach ($result as $line) {
                if (str_contains($line, $expectedError)) {
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                throw new Exception("Method '{$method}' should return error containing '{$expectedError}'");
            }
        }
        echo "  ✅ Parameter validation test passed\n";
        
        // 测试方法签名一致性
        $reflection = new ReflectionClass($commandService);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        
        $commandMethods = array_filter($methods, function($method) {
            $name = $method->getName();
            return (
                in_array($name, array_values(\App\Service\Telegram\Bot\CommandEnum::COMMAND_SET)) ||
                in_array($name, array_values(\App\Service\Telegram\Bot\CommandEnum::COMMAND_SET_CN))
            ) && $name !== '__construct' && $name !== 'setTelegramBot';
        });
        
        foreach ($commandMethods as $method) {
            $parameters = $method->getParameters();
            
            if (count($parameters) !== 3) {
                throw new Exception("Method {$method->getName()} should have 3 parameters");
            }
            
            if ($parameters[0]->getName() !== 'userId') {
                throw new Exception("Method {$method->getName()} first parameter should be 'userId'");
            }
            
            if ($parameters[1]->getName() !== 'params') {
                throw new Exception("Method {$method->getName()} second parameter should be 'params'");
            }
            
            if ($parameters[2]->getName() !== 'recordID') {
                throw new Exception("Method {$method->getName()} third parameter should be 'recordID'");
            }
        }
        echo "  ✅ Method signature consistency test passed\n";
        
        echo "✅ TelegramCommandService tests completed successfully\n";
        
    } catch (Throwable $e) {
        echo "❌ TelegramCommandService test failed:\n";
        echo "   Error: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        
        // 显示堆栈跟踪（仅前3行）
        $trace = explode("\n", $e->getTraceAsString());
        echo "   Stack trace:\n";
        for ($i = 0; $i < min(3, count($trace)); $i++) {
            echo "     " . $trace[$i] . "\n";
        }
    }
}