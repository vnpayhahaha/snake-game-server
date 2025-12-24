<?php

declare(strict_types=1);

/**
 * 测试数据初始化脚本
 * 为业务单元测试提供必要的测试数据
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

echo "🔧 Initializing Test Data for Snake Game\n";
echo "📅 " . date('Y-m-d H:i:s') . "\n";
echo str_repeat('=', 50) . "\n";

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

// 初始化测试数据
initializeTestData();

function initializeTestData(): void
{
    echo "📋 Initializing Test Data:\n";
    echo str_repeat('-', 30) . "\n";
    
    try {
        $container = \Hyperf\Context\ApplicationContext::getContainer();
        $db = $container->get(\Hyperf\DbConnection\Db::class);
        
        // 1. 创建测试租户
        echo "1. Creating test tenant...\n";
        $tenantExists = $db->table('tenant')->where('id', '000001')->exists();
        if (!$tenantExists) {
            $db->table('tenant')->insert([
                'id' => '000001',
                'name' => 'Test Tenant',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "   ✅ Test tenant created\n";
        } else {
            echo "   ✅ Test tenant already exists\n";
        }
        
        // 2. 创建测试游戏群组配置（先创建配置）
        echo "2. Creating test game group config...\n";
        $testChatId = '-1001234567890';
        $configExists = $db->table('game_group_config')->where('tg_chat_id', $testChatId)->exists();
        if (!$configExists) {
            $configId = $db->table('game_group_config')->insertGetId([
                'tenant_id' => '000001',
                'tg_chat_id' => $testChatId,
                'tg_chat_title' => 'Test Snake Game Group',
                'wallet_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx', // 有效的TRON地址格式
                'hot_wallet_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx',
                'hot_wallet_private_key' => 'test_private_key_encrypted',
                'bet_amount' => '5.000000',
                'platform_fee_rate' => '0.1000',
                'wallet_change_status' => 1,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "   ✅ Test game group config created (ID: {$configId})\n";
        } else {
            $config = $db->table('game_group_config')->where('tg_chat_id', $testChatId)->first();
            $configId = $config->id;
            echo "   ✅ Test game group config already exists (ID: {$configId})\n";
        }
        
        // 3. 创建测试游戏群组
        echo "3. Creating test game group...\n";
        $groupExists = $db->table('game_group')->where('tg_chat_id', $testChatId)->exists();
        if (!$groupExists) {
            $groupId = $db->table('game_group')->insertGetId([
                'config_id' => $configId,
                'tg_chat_id' => $testChatId,
                'prize_pool_amount' => '0.000000',
                'version' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "   ✅ Test game group created (ID: {$groupId})\n";
        } else {
            $group = $db->table('game_group')->where('tg_chat_id', $testChatId)->first();
            $groupId = $group->id;
            echo "   ✅ Test game group already exists (ID: {$groupId})\n";
        }
        
        // 4. 创建测试玩家钱包绑定
        echo "4. Creating test player wallet binding...\n";
        $testUserId = 123456789;
        $bindingExists = $db->table('player_wallet_binding')
            ->where('group_id', $groupId)
            ->where('tg_user_id', $testUserId)
            ->exists();
        if (!$bindingExists) {
            $db->table('player_wallet_binding')->insert([
                'group_id' => $groupId,
                'tg_user_id' => $testUserId,
                'tg_username' => 'testuser',
                'tg_first_name' => 'Test',
                'tg_last_name' => 'User',
                'wallet_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx',
                'bind_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "   ✅ Test player wallet binding created\n";
        } else {
            echo "   ✅ Test player wallet binding already exists\n";
        }
        
        // 5. 创建测试蛇身节点
        echo "5. Creating test snake nodes...\n";
        $nodeCount = $db->table('snake_node')->where('group_id', $groupId)->count();
        if ($nodeCount < 5) {
            for ($i = 1; $i <= 5; $i++) {
                $db->table('snake_node')->insert([
                    'group_id' => $groupId,
                    'wallet_cycle' => 1,
                    'ticket_number' => str_pad((string)($i * 11), 2, '0', STR_PAD_LEFT),
                    'ticket_serial_no' => date('Ymd') . '-' . str_pad((string)$i, 3, '0', STR_PAD_LEFT),
                    'player_address' => 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx',
                    'player_tg_username' => 'testuser',
                    'player_tg_user_id' => $testUserId,
                    'amount' => '5.000000',
                    'tx_hash' => 'test_tx_hash_' . $i . '_' . uniqid(),
                    'block_height' => 1000000 + $i,
                    'daily_sequence' => $i,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s', time() - 3600 + $i * 60),
                ]);
            }
            echo "   ✅ Test snake nodes created (5 nodes)\n";
        } else {
            echo "   ✅ Test snake nodes already exist ({$nodeCount} nodes)\n";
        }
        
        // 6. 创建测试中奖记录
        echo "6. Creating test prize records...\n";
        $prizeCount = $db->table('prize_record')->where('group_id', $groupId)->count();
        if ($prizeCount < 2) {
            for ($i = 1; $i <= 2; $i++) {
                $db->table('prize_record')->insert([
                    'group_id' => $groupId,
                    'prize_serial_no' => 'WIN' . $groupId . date('YmdHis') . $i,
                    'wallet_cycle' => 1,
                    'ticket_number' => str_pad((string)($i * 11), 2, '0', STR_PAD_LEFT),
                    'winner_node_id_first' => $i,
                    'winner_node_id_last' => $i + 1,
                    'winner_node_ids' => $i . ',' . ($i + 1),
                    'total_amount' => '10.000000',
                    'platform_fee' => '1.000000',
                    'fee_rate' => '0.1000',
                    'prize_pool' => '9.000000',
                    'prize_amount' => '9.000000',
                    'prize_per_winner' => '4.500000',
                    'pool_remaining' => '0.000000',
                    'winner_count' => 2,
                    'status' => 1,
                    'version' => 0,
                    'created_at' => date('Y-m-d H:i:s', time() - 1800 + $i * 300),
                    'updated_at' => date('Y-m-d H:i:s', time() - 1800 + $i * 300),
                ]);
            }
            echo "   ✅ Test prize records created (2 records)\n";
        } else {
            echo "   ✅ Test prize records already exist ({$prizeCount} records)\n";
        }
        
        echo "\n✅ All test data initialized successfully!\n";
        echo "\n📋 Test Data Summary:\n";
        echo "   • Tenant: 000001 (Test Tenant)\n";
        echo "   • Game Group: {$testChatId} (Test Snake Game Group)\n";
        echo "   • Test User: {$testUserId} (testuser)\n";
        echo "   • Wallet Address: TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx\n";
        echo "   • Snake Nodes: " . $db->table('snake_node')->where('group_id', $groupId)->count() . " nodes\n";
        echo "   • Prize Records: " . $db->table('prize_record')->where('group_id', $groupId)->count() . " records\n";
        
        echo "\n🧪 Ready for testing! Run:\n";
        echo "   php run_business_unit_tests.php\n";
        echo "   php bin/hyperf.php telegram:test\n";
        
    } catch (Throwable $e) {
        echo "❌ Failed to initialize test data:\n";
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

echo "\n📅 Initialization completed: " . date('Y-m-d H:i:s') . "\n";