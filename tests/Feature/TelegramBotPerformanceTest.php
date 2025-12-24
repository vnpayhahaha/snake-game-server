<?php

declare(strict_types=1);

namespace HyperfTests\Feature;

use App\Service\Telegram\Bot\TelegramCommandService;
use App\Service\Telegram\Bot\CommandEnum;
use Hyperf\Testing\TestCase;

/**
 * Telegram Bot 性能测试
 */
class TelegramBotPerformanceTest extends TestCase
{
    private TelegramCommandService $commandService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->commandService = $this->container->get(TelegramCommandService::class);
        
        // 设置模拟的Telegram Bot
        $this->setupMockTelegramBot();
    }

    /**
     * 设置模拟的Telegram Bot
     */
    private function setupMockTelegramBot(): void
    {
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
    }

    /**
     * 测试指令执行性能
     */
    public function testCommandExecutionPerformance(): void
    {
        $commands = [
            ['Start', []],
            ['Help', []],
            ['Rules', []],
            ['cnStart', []],
            ['cnHelp', []],
            ['cnRules', []],
        ];

        foreach ($commands as [$method, $params]) {
            $startTime = microtime(true);
            
            $result = $this->commandService->{$method}(123456789, $params, 1);
            
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // 转换为毫秒

            $this->assertIsArray($result);
            $this->assertLessThan(100, $executionTime, "Method {$method} should execute in less than 100ms");
            $this->assertGreaterThan(0, $executionTime);

            echo sprintf("Method %s executed in %.2fms\n", $method, $executionTime);
        }
    }

    /**
     * 测试批量指令执行性能
     */
    public function testBatchCommandExecutionPerformance(): void
    {
        $batchSize = 100;
        $commands = ['Start', 'Help', 'Rules', 'cnStart', 'cnHelp', 'cnRules'];
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $batchSize; $i++) {
            $method = $commands[$i % count($commands)];
            $result = $this->commandService->{$method}(123456789, [], $i);
            $this->assertIsArray($result);
        }
        
        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;
        $averageTime = $totalTime / $batchSize;

        $this->assertLessThan(50, $averageTime, 'Average execution time should be less than 50ms');
        $this->assertLessThan(5000, $totalTime, 'Total time should be less than 5 seconds');

        echo sprintf("Batch execution: %d commands in %.2fms (avg: %.2fms per command)\n", 
            $batchSize, $totalTime, $averageTime);
    }

    /**
     * 测试内存使用情况
     */
    public function testMemoryUsage(): void
    {
        $initialMemory = memory_get_usage(true);
        
        // 执行多个指令
        for ($i = 0; $i < 50; $i++) {
            $this->commandService->Start(123456789, [], $i);
            $this->commandService->cnStart(123456789, [], $i);
            $this->commandService->Help(123456789, [], $i);
            $this->commandService->cnHelp(123456789, [], $i);
        }
        
        $finalMemory = memory_get_usage(true);
        $memoryIncrease = $finalMemory - $initialMemory;
        $memoryIncreaseMB = $memoryIncrease / 1024 / 1024;

        $this->assertLessThan(10, $memoryIncreaseMB, 'Memory increase should be less than 10MB');

        echo sprintf("Memory usage increased by %.2fMB\n", $memoryIncreaseMB);
    }

    /**
     * 测试CommandEnum性能
     */
    public function testCommandEnumPerformance(): void
    {
        $commands = array_keys(CommandEnum::COMMAND_SET);
        $chineseCommands = array_keys(CommandEnum::COMMAND_SET_CN);
        $allCommands = array_merge($commands, $chineseCommands);
        
        $iterations = 1000;
        
        // 测试isCommand性能
        $startTime = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            foreach ($allCommands as $command) {
                CommandEnum::isCommand($command);
            }
        }
        $endTime = microtime(true);
        $isCommandTime = ($endTime - $startTime) * 1000;

        // 测试getCommand性能
        $startTime = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            foreach ($allCommands as $command) {
                CommandEnum::getCommand($command);
            }
        }
        $endTime = microtime(true);
        $getCommandTime = ($endTime - $startTime) * 1000;

        $totalCommands = count($allCommands) * $iterations;
        
        $this->assertLessThan(1000, $isCommandTime, 'isCommand total time should be less than 1 second');
        $this->assertLessThan(1000, $getCommandTime, 'getCommand total time should be less than 1 second');

        echo sprintf("CommandEnum performance:\n");
        echo sprintf("  isCommand: %.2fms for %d calls (%.4fms per call)\n", 
            $isCommandTime, $totalCommands, $isCommandTime / $totalCommands);
        echo sprintf("  getCommand: %.2fms for %d calls (%.4fms per call)\n", 
            $getCommandTime, $totalCommands, $getCommandTime / $totalCommands);
    }

    /**
     * 测试帮助信息生成性能
     */
    public function testHelpGenerationPerformance(): void
    {
        $iterations = 100;
        
        // 测试英文帮助生成
        $startTime = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $help = CommandEnum::getHelpReply(false);
            $this->assertIsArray($help);
            $this->assertNotEmpty($help);
        }
        $endTime = microtime(true);
        $englishHelpTime = ($endTime - $startTime) * 1000;

        // 测试中文帮助生成
        $startTime = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $help = CommandEnum::getHelpReply(true);
            $this->assertIsArray($help);
            $this->assertNotEmpty($help);
        }
        $endTime = microtime(true);
        $chineseHelpTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(500, $englishHelpTime, 'English help generation should be less than 500ms');
        $this->assertLessThan(500, $chineseHelpTime, 'Chinese help generation should be less than 500ms');

        echo sprintf("Help generation performance:\n");
        echo sprintf("  English: %.2fms for %d calls (%.2fms per call)\n", 
            $englishHelpTime, $iterations, $englishHelpTime / $iterations);
        echo sprintf("  Chinese: %.2fms for %d calls (%.2fms per call)\n", 
            $chineseHelpTime, $iterations, $chineseHelpTime / $iterations);
    }

    /**
     * 测试并发执行模拟
     */
    public function testConcurrentExecutionSimulation(): void
    {
        $userIds = range(100000000, 100000099); // 模拟100个用户
        $commands = ['Start', 'Help', 'Rules'];
        
        $startTime = microtime(true);
        
        foreach ($userIds as $userId) {
            foreach ($commands as $command) {
                $result = $this->commandService->{$command}($userId, [], rand(1, 1000));
                $this->assertIsArray($result);
            }
        }
        
        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;
        $totalOperations = count($userIds) * count($commands);
        $averageTime = $totalTime / $totalOperations;

        $this->assertLessThan(10, $averageTime, 'Average execution time should be less than 10ms');
        $this->assertLessThan(30000, $totalTime, 'Total time should be less than 30 seconds');

        echo sprintf("Concurrent simulation: %d operations in %.2fms (avg: %.2fms per operation)\n", 
            $totalOperations, $totalTime, $averageTime);
    }

    /**
     * 测试异常处理性能影响
     */
    public function testExceptionHandlingPerformance(): void
    {
        $iterations = 100;
        
        // 测试正常执行
        $startTime = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $this->commandService->Start(123456789, [], $i);
        }
        $endTime = microtime(true);
        $normalTime = ($endTime - $startTime) * 1000;

        // 测试带异常处理的执行（通过无效参数触发异常处理路径）
        $startTime = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $this->commandService->BindWallet(123456789, [], $i); // 无参数会触发异常处理
        }
        $endTime = microtime(true);
        $exceptionTime = ($endTime - $startTime) * 1000;

        $performanceImpact = ($exceptionTime - $normalTime) / $normalTime * 100;

        $this->assertLessThan(200, $performanceImpact, 'Exception handling performance impact should be less than 200%');

        echo sprintf("Exception handling performance impact:\n");
        echo sprintf("  Normal execution: %.2fms for %d calls\n", $normalTime, $iterations);
        echo sprintf("  With exceptions: %.2fms for %d calls\n", $exceptionTime, $iterations);
        echo sprintf("  Performance impact: %.1f%%\n", $performanceImpact);
    }
}