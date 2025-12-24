<?php

declare(strict_types=1);

namespace HyperfTests\Unit;

use App\Service\Telegram\Bot\TelegramCommandService;
use App\Service\Telegram\Bot\CommandEnum;
use Hyperf\Testing\TestCase;

/**
 * TelegramCommandService 单元测试
 */
class TelegramCommandServiceTest extends TestCase
{
    private TelegramCommandService $commandService;
    private $mockTelegramBot;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 创建模拟的Telegram Bot
        $this->mockTelegramBot = $this->createMockTelegramBot();
        
        // 创建服务实例
        $this->commandService = $this->container->get(TelegramCommandService::class);
        $this->commandService->setTelegramBot($this->mockTelegramBot);
    }

    /**
     * 创建模拟的Telegram Bot
     */
    private function createMockTelegramBot()
    {
        return new class {
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
    }

    /**
     * 测试Start指令 - 英文版本
     */
    public function testStartCommandReturnsEnglishWelcomeMessage(): void
    {
        $result = $this->commandService->Start(123456789, [], 1);
        
        $this->assertIsArray($result);
        $this->assertStringContainsString('Welcome to Snake Chain Game!', $result[0]);
        $this->assertContains('🎮 Good luck and have fun!', $result);
    }

    /**
     * 测试开始指令 - 中文版本
     */
    public function testCnStartCommandReturnsChineseWelcomeMessage(): void
    {
        $result = $this->commandService->cnStart(123456789, [], 1);
        
        $this->assertIsArray($result);
        $this->assertStringContainsString('欢迎来到贪吃蛇链上游戏！', $result[0]);
        $this->assertContains('🎮 祝您好运，玩得开心！', $result);
    }

    /**
     * 测试Help指令 - 英文版本
     */
    public function testHelpCommandReturnsEnglishHelp(): void
    {
        $result = $this->commandService->Help(123456789, [], 1);
        
        $this->assertIsArray($result);
        $this->assertStringContainsString('Snake Chain Game Command List', $result[0]);
    }

    /**
     * 测试帮助指令 - 中文版本
     */
    public function testCnHelpCommandReturnsChineseHelp(): void
    {
        $result = $this->commandService->cnHelp(123456789, [], 1);
        
        $this->assertIsArray($result);
        $this->assertStringContainsString('Snake Chain Game 指令列表', $result[0]);
    }

    /**
     * 测试Rules指令 - 英文游戏规则
     */
    public function testRulesCommandReturnsEnglishRules(): void
    {
        $result = $this->commandService->Rules(123456789, [], 1);
        
        $this->assertIsArray($result);
        $this->assertStringContainsString('Snake Chain Game Rules', $result[0]);
        $this->assertContains('1. Send specified amount of TRX to group payment address to buy tickets', $result);
    }

    /**
     * 测试游戏规则指令 - 中文游戏规则
     */
    public function testCnRulesCommandReturnsChineseRules(): void
    {
        $result = $this->commandService->cnRules(123456789, [], 1);
        
        $this->assertIsArray($result);
        $this->assertStringContainsString('贪吃蛇链上游戏规则', $result[0]);
        $this->assertContains('1. 向群组收款地址发送指定金额的TRX购买彩票', $result);
    }

    /**
     * 测试BindWallet指令 - 参数验证
     */
    public function testBindWalletCommandValidatesParameters(): void
    {
        // 测试无参数
        $result = $this->commandService->BindWallet(123456789, [], 1);
        $this->assertIsArray($result);
        $this->assertStringContainsString('Invalid parameters', $result[0]);

        // 测试无效地址
        $result = $this->commandService->BindWallet(123456789, ['invalid_address'], 1);
        $this->assertIsArray($result);
        $this->assertStringContainsString('Invalid TRON wallet address', $result[0]);
    }

    /**
     * 测试绑定钱包指令 - 中文参数验证
     */
    public function testCnBindWalletCommandValidatesParameters(): void
    {
        // 测试无参数
        $result = $this->commandService->cnBindWallet(123456789, [], 1);
        $this->assertIsArray($result);
        $this->assertStringContainsString('参数错误', $result[0]);

        // 测试无效地址
        $result = $this->commandService->cnBindWallet(123456789, ['invalid_address'], 1);
        $this->assertIsArray($result);
        $this->assertStringContainsString('无效的TRON钱包地址', $result[0]);
    }

    /**
     * 测试方法签名一致性
     */
    public function testAllCommandMethodsHaveConsistentSignature(): void
    {
        $reflection = new \ReflectionClass(TelegramCommandService::class);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        
        $commandMethods = array_filter($methods, function($method) {
            $name = $method->getName();
            return (
                in_array($name, array_values(CommandEnum::COMMAND_SET)) ||
                in_array($name, array_values(CommandEnum::COMMAND_SET_CN))
            ) && $name !== '__construct' && $name !== 'setTelegramBot';
        });

        foreach ($commandMethods as $method) {
            $parameters = $method->getParameters();
            
            $this->assertCount(3, $parameters, "Method {$method->getName()} should have 3 parameters");
            $this->assertEquals('userId', $parameters[0]->getName(), "First parameter should be 'userId'");
            $this->assertEquals('params', $parameters[1]->getName(), "Second parameter should be 'params'");
            $this->assertEquals('recordID', $parameters[2]->getName(), "Third parameter should be 'recordID'");
                
            $returnType = $method->getReturnType();
            $this->assertNotNull($returnType, "Method {$method->getName()} should have return type");
            
            // 处理联合类型 (string|array)
            if ($returnType instanceof \ReflectionUnionType) {
                $typeNames = array_map(fn($type) => $type->getName(), $returnType->getTypes());
                $this->assertContains('array', $typeNames, "Method {$method->getName()} should return array or string|array");
            } else {
                $this->assertStringContainsString('array', $returnType->getName(), "Method {$method->getName()} should return array or string|array");
            }
        }
    }
}