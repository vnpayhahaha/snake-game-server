-- Telegram 机器人测试模拟数据
-- 用于本地开发环境调试

-- 清理现有测试数据
DELETE FROM game_group_config WHERE tg_chat_id = -1001234567890;
DELETE FROM game_group WHERE tg_chat_id = -1001234567890;
DELETE FROM player_wallet_binding WHERE group_id IN (SELECT id FROM game_group_config WHERE tg_chat_id = -1001234567890);
DELETE FROM snake_node WHERE group_id IN (SELECT id FROM game_group_config WHERE tg_chat_id = -1001234567890);
DELETE FROM prize_record WHERE group_id IN (SELECT id FROM game_group_config WHERE tg_chat_id = -1001234567890);

-- 插入测试租户（如果不存在）
INSERT IGNORE INTO tenant (id, name, package_id, user_id, account_count, contact_name, contact_phone, status, created_by, updated_by, created_at, updated_at, remark)
VALUES (1, 'Test Tenant', 1, 1, 100, 'Test Contact', '13800138000', 1, 1, 1, NOW(), NOW(), 'Test tenant for development');

-- 插入测试群组配置
INSERT INTO game_group_config (
    tenant_id, tg_chat_id, tg_chat_title, wallet_address, wallet_change_count,
    pending_wallet_address, wallet_change_status, wallet_change_start_at, wallet_change_end_at,
    hot_wallet_address, hot_wallet_private_key, bet_amount, platform_fee_rate, status,
    created_at, updated_at
) VALUES (
    '000001', -1001234567890, 'Test Snake Game Group', 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx', 0,
    NULL, 1, NULL, NULL,
    'TTestHotWallet123456789012345678901', 'encrypted_private_key_here', 5.00, 0.10, 1,
    NOW(), NOW()
);

-- 获取刚插入的配置ID
SET @config_id = LAST_INSERT_ID();

-- 插入测试游戏群组状态
INSERT INTO game_group (
    config_id, tg_chat_id, prize_pool_amount, current_snake_nodes, last_snake_nodes,
    last_prize_nodes, last_prize_amount, last_prize_address, last_prize_serial_no,
    last_prize_at, version, created_at, updated_at
) VALUES (
    @config_id, -1001234567890, 25.50, '[1,2,3,4,5]', '[1,2,3]',
    '[2,3]', 15.30, 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx,TTestWallet2', '20250108-001',
    NOW() - INTERVAL 1 HOUR, 1, NOW(), NOW()
);

-- 插入测试玩家钱包绑定
INSERT INTO player_wallet_binding (
    group_id, tg_user_id, tg_username, tg_first_name, tg_last_name,
    wallet_address, bind_at, created_at, updated_at
) VALUES 
(
    @config_id, 123456789, 'testuser', 'Test', 'User',
    'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY, NOW()
),
(
    @config_id, 987654321, 'testuser2', 'Test2', 'User2',
    'TTestWallet2345678901234567890123', NOW() - INTERVAL 2 DAYS, NOW() - INTERVAL 2 DAYS, NOW()
);

-- 插入测试蛇身节点
INSERT INTO snake_node (
    group_id, ticket_serial_no, ticket_number, player_address, player_tg_user_id,
    player_tg_username, amount, tx_hash, block_height, block_timestamp,
    is_winner, prize_amount, created_at, updated_at
) VALUES 
(
    @config_id, '20250108-001', '09', 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx', 123456789,
    'testuser', 5.00, 'mock_tx_hash_001', 45678901, NOW() - INTERVAL 5 HOURS,
    1, 7.50, NOW() - INTERVAL 5 HOURS, NOW() - INTERVAL 5 HOURS
),
(
    @config_id, '20250108-002', '15', 'TTestWallet2345678901234567890123', 987654321,
    'testuser2', 5.00, 'mock_tx_hash_002', 45678902, NOW() - INTERVAL 4 HOURS,
    0, 0.00, NOW() - INTERVAL 4 HOURS, NOW() - INTERVAL 4 HOURS
),
(
    @config_id, '20250108-003', '09', 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx', 123456789,
    'testuser', 5.00, 'mock_tx_hash_003', 45678903, NOW() - INTERVAL 3 HOURS,
    1, 7.80, NOW() - INTERVAL 3 HOURS, NOW() - INTERVAL 3 HOURS
),
(
    @config_id, '20250108-004', '22', 'TTestWallet2345678901234567890123', 987654321,
    'testuser2', 5.00, 'mock_tx_hash_004', 45678904, NOW() - INTERVAL 2 HOURS,
    0, 0.00, NOW() - INTERVAL 2 HOURS, NOW() - INTERVAL 2 HOURS
),
(
    @config_id, '20250108-005', '33', 'TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx', 123456789,
    'testuser', 5.00, 'mock_tx_hash_005', 45678905, NOW() - INTERVAL 1 HOUR,
    0, 0.00, NOW() - INTERVAL 1 HOUR, NOW() - INTERVAL 1 HOUR
);

-- 插入测试中奖记录
INSERT INTO prize_record (
    group_id, prize_serial_no, ticket_number, winner_node_ids, winner_count,
    prize_amount, prize_per_winner, platform_fee, status, created_at, updated_at
) VALUES 
(
    @config_id, '20250108-001', '09', '[1,3]', 2,
    15.30, 7.65, 1.70, 2, NOW() - INTERVAL 3 HOURS, NOW() - INTERVAL 3 HOURS
),
(
    @config_id, '20250108-002', '15', '[2]', 1,
    9.00, 9.00, 1.00, 2, NOW() - INTERVAL 1 HOUR, NOW() - INTERVAL 1 HOUR
);

-- 插入测试Telegram指令消息记录
INSERT INTO telegram_command_message_record (
    chat_id, message_id, command, chat_name, user_id, username, nickname,
    original_message, response_message, status, created_at, updated_at
) VALUES 
(
    -1001234567890, 12345, 'start', 'Test Snake Game Group', 123456789, 'testuser', 'Test User',
    '/start', 'Welcome to Snake Chain Game!', 2, NOW() - INTERVAL 1 HOUR, NOW() - INTERVAL 1 HOUR
),
(
    -1001234567890, 12346, 'bindwallet', 'Test Snake Game Group', 123456789, 'testuser', 'Test User',
    '/bindwallet TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx', 'Wallet address bound successfully!', 2, NOW() - INTERVAL 30 MINUTES, NOW() - INTERVAL 30 MINUTES
);

-- 显示插入的测试数据统计
SELECT 
    'game_group_config' as table_name, 
    COUNT(*) as count 
FROM game_group_config 
WHERE tg_chat_id = -1001234567890

UNION ALL

SELECT 
    'game_group' as table_name, 
    COUNT(*) as count 
FROM game_group 
WHERE tg_chat_id = -1001234567890

UNION ALL

SELECT 
    'player_wallet_binding' as table_name, 
    COUNT(*) as count 
FROM player_wallet_binding 
WHERE group_id = @config_id

UNION ALL

SELECT 
    'snake_node' as table_name, 
    COUNT(*) as count 
FROM snake_node 
WHERE group_id = @config_id

UNION ALL

SELECT 
    'prize_record' as table_name, 
    COUNT(*) as count 
FROM prize_record 
WHERE group_id = @config_id;

-- 显示测试数据概览
SELECT 
    '=== Test Data Overview ===' as info;

SELECT 
    'Group Config:' as info,
    tg_chat_id,
    tg_chat_title,
    wallet_address,
    bet_amount,
    platform_fee_rate
FROM game_group_config 
WHERE tg_chat_id = -1001234567890;

SELECT 
    'Current Snake:' as info,
    prize_pool_amount,
    current_snake_nodes,
    last_prize_amount
FROM game_group 
WHERE tg_chat_id = -1001234567890;

SELECT 
    'Player Bindings:' as info,
    tg_user_id,
    tg_username,
    wallet_address
FROM player_wallet_binding 
WHERE group_id = @config_id;

SELECT 
    'Snake Nodes:' as info,
    ticket_serial_no,
    ticket_number,
    player_tg_username,
    amount,
    is_winner,
    prize_amount
FROM snake_node 
WHERE group_id = @config_id
ORDER BY created_at;