<?php

declare(strict_types=1);

namespace HyperfTests\Unit;

use App\Service\Telegram\Bot\CommandEnum;
use PHPUnit\Framework\TestCase;

/**
 * CommandEnum 单元测试
 */
class CommandEnumTest extends TestCase
{
    /**
     * 测试英文指令映射
     */
    public function testMapsEnglishCommandsCorrectly(): void
    {
        $englishCommands = [
            'start' => 'Start',
            'help' => 'Help',
            'bindwallet' => 'BindWallet',
            'unbindwallet' => 'UnbindWallet',
            'mywallet' => 'MyWallet',
            'snake' => 'Snake',
            'mytickets' => 'MyTickets',
            'ticket' => 'Ticket',
            'myprizes' => 'MyPrizes',
            'history' => 'History',
            'stats' => 'Stats',
            'rules' => 'Rules',
            'address' => 'Address',
            'bind' => 'BindTenant',
            'wallet' => 'SetWallet',
            'cancelwallet' => 'CancelWallet',
            'setbet' => 'SetBet',
            'setfee' => 'SetFee',
            'info' => 'Info',
        ];

        foreach ($englishCommands as $command => $expectedMethod) {
            $this->assertTrue(CommandEnum::isCommand($command), "Command '{$command}' should be valid");
            $this->assertEquals($expectedMethod, CommandEnum::getCommand($command), "Command '{$command}' should map to '{$expectedMethod}'");
        }
    }

    /**
     * 测试中文指令映射
     */
    public function testMapsChineseCommandsCorrectly(): void
    {
        $chineseCommands = [
            '开始' => 'cnStart',
            '帮助' => 'cnHelp',
            '绑定钱包' => 'cnBindWallet',
            '解绑钱包' => 'cnUnbindWallet',
            '我的钱包' => 'cnMyWallet',
            '蛇身' => 'cnSnake',
            '我的购彩' => 'cnMyTickets',
            '查询票号' => 'cnTicket',
            '我的中奖' => 'cnMyPrizes',
            '历史中奖' => 'cnHistory',
            '游戏统计' => 'cnStats',
            '游戏规则' => 'cnRules',
            '收款地址' => 'cnAddress',
            '绑定租户' => 'cnBindTenant',
            '设置钱包' => 'cnSetWallet',
            '取消钱包变更' => 'cnCancelWallet',
            '设置投注' => 'cnSetBet',
            '设置手续费' => 'cnSetFee',
            '群组配置' => 'cnInfo',
        ];

        foreach ($chineseCommands as $command => $expectedMethod) {
            $this->assertTrue(CommandEnum::isCommand($command), "Chinese command '{$command}' should be valid");
            $this->assertEquals($expectedMethod, CommandEnum::getCommand($command), "Chinese command '{$command}' should map to '{$expectedMethod}'");
        }
    }

    /**
     * 测试无效指令
     */
    public function testRejectsInvalidCommands(): void
    {
        $invalidCommands = [
            'invalid',
            'notexist',
            '无效指令',
            '',
            // 移除大小写测试，因为应该是不敏感的
        ];

        foreach ($invalidCommands as $command) {
            $this->assertFalse(CommandEnum::isCommand($command), "Command '{$command}' should be invalid");
            $this->assertEquals('', CommandEnum::getCommand($command), "Invalid command '{$command}' should return empty string");
        }
    }

    /**
     * 测试大小写不敏感性
     */
    public function testHandlesCaseInsensitivityCorrectly(): void
    {
        // 英文指令应该不区分大小写
        $this->assertTrue(CommandEnum::isCommand('START'), 'Uppercase START should be valid');
        $this->assertTrue(CommandEnum::isCommand('start'), 'Lowercase start should be valid');
        $this->assertTrue(CommandEnum::isCommand('Help'), 'Capitalized Help should be valid');
        $this->assertTrue(CommandEnum::isCommand('help'), 'Lowercase help should be valid');
        
        // 验证方法名映射正确
        $this->assertEquals('Start', CommandEnum::getCommand('START'));
        $this->assertEquals('Start', CommandEnum::getCommand('start'));
        $this->assertEquals('Help', CommandEnum::getCommand('HELP'));
        $this->assertEquals('Help', CommandEnum::getCommand('help'));
    }

    /**
     * 测试英文帮助信息
     */
    public function testGeneratesEnglishHelpCorrectly(): void
    {
        $help = CommandEnum::getHelpReply(false);
        
        $this->assertIsArray($help);
        $this->assertNotEmpty($help);
        $this->assertStringContainsString('Snake Chain Game Command List', $help[0]);
        $this->assertContains('💡 Tip: Send TRON to the payment address to play automatically', $help);
    }

    /**
     * 测试中文帮助信息
     */
    public function testGeneratesChineseHelpCorrectly(): void
    {
        $help = CommandEnum::getHelpReply(true);
        
        $this->assertIsArray($help);
        $this->assertNotEmpty($help);
        $this->assertStringContainsString('Snake Chain Game 指令列表', $help[0]);
        $this->assertContains('💡 提示：发送TRON到收款地址即可自动购彩', $help);
    }

    /**
     * 测试队列名称常量
     */
    public function testDefinesQueueNamesCorrectly(): void
    {
        $this->assertEquals('telegram-command-run-queue', CommandEnum::TELEGRAM_COMMAND_RUN_QUEUE_NAME);
        $this->assertEquals('telegram-notice-queue', CommandEnum::TELEGRAM_NOTICE_QUEUE_NAME);
        $this->assertEquals('tron-tx-process-queue', CommandEnum::TRON_TX_PROCESS_QUEUE_NAME);
        $this->assertEquals('prize-dispatch-queue', CommandEnum::PRIZE_DISPATCH_QUEUE_NAME);
    }

    /**
     * 测试指令描述映射完整性
     */
    public function testHasCompleteCommandDescriptions(): void
    {
        // 检查所有英文指令都有描述
        foreach (CommandEnum::COMMAND_SET as $command => $method) {
            $this->assertArrayHasKey($command, CommandEnum::$commandDescMap, "English command '{$command}' should have description");
            $this->assertNotEmpty(CommandEnum::$commandDescMap[$command], "English command '{$command}' description should not be empty");
        }

        // 检查所有中文指令都有描述
        foreach (CommandEnum::COMMAND_SET_CN as $command => $method) {
            $this->assertArrayHasKey($command, CommandEnum::$commandDescCnMap, "Chinese command '{$command}' should have description");
            $this->assertNotEmpty(CommandEnum::$commandDescCnMap[$command], "Chinese command '{$command}' description should not be empty");
        }
    }

    /**
     * 测试指令数量一致性
     */
    public function testHasConsistentCommandCounts(): void
    {
        $englishCount = count(CommandEnum::COMMAND_SET);
        $chineseCount = count(CommandEnum::COMMAND_SET_CN);
        $englishDescCount = count(CommandEnum::$commandDescMap);
        $chineseDescCount = count(CommandEnum::$commandDescCnMap);

        $this->assertEquals($englishCount, $chineseCount, 'English and Chinese command counts should match');
        $this->assertEquals($englishCount, $englishDescCount, 'English commands and descriptions counts should match');
        $this->assertEquals($chineseCount, $chineseDescCount, 'Chinese commands and descriptions counts should match');
    }

    /**
     * 测试方法名格式
     */
    public function testFollowsMethodNamingConventions(): void
    {
        // 英文方法名应该是PascalCase
        foreach (CommandEnum::COMMAND_SET as $command => $method) {
            $this->assertMatchesRegularExpression('/^[A-Z][a-zA-Z]*$/', $method, "English method '{$method}' should be PascalCase");
            $this->assertStringStartsNotWith('cn', $method, "English method '{$method}' should not start with 'cn'");
        }

        // 中文方法名应该以cn开头
        foreach (CommandEnum::COMMAND_SET_CN as $command => $method) {
            $this->assertStringStartsWith('cn', $method, "Chinese method '{$method}' should start with 'cn'");
            $this->assertMatchesRegularExpression('/^cn[A-Z][a-zA-Z]*$/', $method, "Chinese method '{$method}' should follow cnPascalCase pattern");
        }
    }

    /**
     * 测试特殊字符处理
     */
    public function testHandlesSpecialCharactersInCommands(): void
    {
        // 测试包含特殊字符的中文指令
        $this->assertTrue(CommandEnum::isCommand('取消钱包变更'), 'Command with special characters should be valid');
        $this->assertEquals('cnCancelWallet', CommandEnum::getCommand('取消钱包变更'), 'Special character command should map correctly');
    }

    /**
     * 测试边界情况
     */
    public function testHandlesEdgeCases(): void
    {
        // 空字符串
        $this->assertFalse(CommandEnum::isCommand(''), 'Empty string should be invalid');
        $this->assertEquals('', CommandEnum::getCommand(''), 'Empty string should return empty method');

        // 空格
        $this->assertFalse(CommandEnum::isCommand(' '), 'Space should be invalid');
        $this->assertEquals('', CommandEnum::getCommand(' '), 'Space should return empty method');

        // 数字
        $this->assertFalse(CommandEnum::isCommand('123'), 'Numbers should be invalid');
        $this->assertEquals('', CommandEnum::getCommand('123'), 'Numbers should return empty method');
    }
}