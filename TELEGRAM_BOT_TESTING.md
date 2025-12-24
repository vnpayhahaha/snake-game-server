# Telegram 机器人指令调试测试指南

## 概述

本文档介绍如何在本地开发环境中调试和测试 Telegram 机器人指令功能。

## 项目完整性检查

### 已修复的问题

1. ✅ **GameGroupConfigRepository::getConfigByChatId()** - 已添加缺失的方法
2. ✅ **Tenant 模型引用** - 已修复为正确的插件路径
3. ✅ **多语言适配** - 英文指令返回英文，中文指令返回中文
4. ✅ **异步队列处理** - 完整的队列架构实现
5. ✅ **业务服务完善** - PlayerWalletBindingService 和 SnakeService 完整实现

### 项目结构验证

```
snake-game-server/
├── app/
│   ├── Service/
│   │   ├── Telegram/Bot/
│   │   │   ├── TelegramCommandService.php ✅
│   │   │   ├── TelegramService.php ✅
│   │   │   └── CommandEnum.php ✅
│   │   ├── Player/
│   │   │   └── PlayerWalletBindingService.php ✅
│   │   └── Snake/
│   │       └── SnakeService.php ✅
│   ├── Repository/
│   │   ├── Game/
│   │   │   ├── GameGroupConfigRepository.php ✅
│   │   │   └── GameGroupRepository.php ✅
│   │   └── Player/
│   │       └── PlayerWalletBindingRepository.php ✅
│   └── Model/
│       ├── Game/
│       │   ├── GameGroupConfig.php ✅
│       │   └── GameGroup.php ✅
│       └── Player/
│           └── PlayerWalletBinding.php ✅
├── test_telegram_commands.php ✅ (完整测试脚本)
├── simple_test.php ✅ (简化测试脚本)
├── mock_data.sql ✅ (模拟数据)
└── TELEGRAM_BOT_TESTING.md ✅ (本文档)
```

## 测试环境准备

### 1. Docker 环境

```bash
# 进入 Docker 容器
docker exec -it hyper bash

# 进入项目目录
cd /data/project/snake-game/snake-game-server

# 检查 PHP 版本和扩展
php -v
php -m | grep -E "(redis|pdo|json)"
```

### 2. 数据库准备

```bash
# 导入模拟数据
mysql -u root -p snake_game < mock_data.sql

# 或者通过 Hyperf 命令
php bin/hyperf.php migrate
```

### 3. Redis 准备

```bash
# 检查 Redis 连接
redis-cli ping

# 清理测试队列
redis-cli del telegram-command-run-queue
redis-cli del telegram-notice-queue
```

## 测试方法

### 方法一：简化测试（推荐）

不依赖数据库和容器，直接测试核心逻辑：

```bash
# 运行简化测试
php simple_test.php
```

**测试内容：**
- ✅ 指令映射验证
- ✅ 多语言帮助信息
- ✅ TRON 地址验证
- ✅ 模拟指令执行
- ✅ 语言差异检查

### 方法二：完整测试

依赖 Hyperf 容器和数据库：

```bash
# 运行所有测试
php test_telegram_commands.php

# 测试特定指令
php test_telegram_commands.php start
php test_telegram_commands.php bindwallet TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx
php test_telegram_commands.php 绑定钱包 TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx
```

### 方法三：手动测试

直接在 Hyperf 控制台中测试：

```bash
# 启动 Hyperf 控制台
php bin/hyperf.php

# 在控制台中执行
use App\Service\Telegram\Bot\CommandEnum;
CommandEnum::isCommand('start');
CommandEnum::getCommand('绑定钱包');
```

## 测试用例

### 基础指令测试

| 指令 | 英文 | 中文 | 预期结果 |
|------|------|------|----------|
| `/start` | ✅ | `/开始` | 欢迎消息（英文/中文） |
| `/help` | ✅ | `/帮助` | 帮助信息（英文/中文） |

### 钱包指令测试

| 指令 | 参数 | 预期结果 |
|------|------|----------|
| `/bindwallet` | `TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx` | 绑定成功（英文） |
| `/绑定钱包` | `TLyqjmNS1aEd6d3UXAN9C2jmGFcykVcqVx` | 绑定成功（中文） |
| `/mywallet` | - | 显示钱包信息（英文） |
| `/我的钱包` | - | 显示钱包信息（中文） |
| `/unbindwallet` | - | 解绑成功（英文） |
| `/解绑钱包` | - | 解绑成功（中文） |

### 游戏查询指令测试

| 指令 | 预期结果 |
|------|----------|
| `/snake` | 蛇身状态（英文） |
| `/蛇身` | 蛇身状态（中文） |
| `/mytickets` | 购彩记录（英文） |
| `/我的购彩` | 购彩记录（中文） |
| `/stats` | 游戏统计（英文） |
| `/游戏统计` | 游戏统计（中文） |

### 管理员指令测试

| 指令 | 参数 | 预期结果 |
|------|------|----------|
| `/bind` | `000001` | 绑定租户（英文） |
| `/绑定租户` | `000001` | 绑定租户（中文） |
| `/setbet` | `5` | 设置投注金额（英文） |
| `/设置投注` | `5` | 设置投注金额（中文） |
| `/info` | - | 群组配置（英文） |
| `/群组配置` | - | 群组配置（中文） |

## 调试技巧

### 1. 日志查看

```bash
# 查看 Hyperf 日志
tail -f runtime/logs/hyperf.log

# 查看错误日志
tail -f runtime/logs/error.log
```

### 2. Redis 队列监控

```bash
# 监控队列长度
redis-cli llen telegram-command-run-queue
redis-cli llen telegram-notice-queue

# 查看队列内容
redis-cli lrange telegram-command-run-queue 0 -1
```

### 3. 数据库查询

```sql
-- 查看测试数据
SELECT * FROM game_group_config WHERE tg_chat_id = -1001234567890;
SELECT * FROM player_wallet_binding WHERE tg_user_id = 123456789;
SELECT * FROM snake_node WHERE group_id = 1 ORDER BY created_at DESC LIMIT 5;
```

### 4. 性能测试

```bash
# 测试指令执行时间
time php simple_test.php

# 内存使用情况
php -d memory_limit=128M simple_test.php
```

## 常见问题

### 1. 容器连接问题

```bash
# 检查容器状态
docker ps | grep hyper

# 重启容器
docker restart hyper
```

### 2. 数据库连接问题

```bash
# 检查数据库配置
cat .env | grep DB_

# 测试数据库连接
php -r "
try {
    \$pdo = new PDO('mysql:host=localhost;dbname=snake_game', 'root', 'password');
    echo 'Database connected successfully\n';
} catch (Exception \$e) {
    echo 'Database connection failed: ' . \$e->getMessage() . '\n';
}
"
```

### 3. Redis 连接问题

```bash
# 检查 Redis 配置
cat .env | grep REDIS_

# 测试 Redis 连接
redis-cli -h localhost -p 6379 ping
```

### 4. 权限问题

```bash
# 检查文件权限
ls -la test_telegram_commands.php
chmod +x test_telegram_commands.php

# 检查目录权限
ls -la runtime/logs/
```

## 预期测试结果

### 成功指标

- ✅ 所有指令映射正确
- ✅ 英文指令返回英文消息
- ✅ 中文指令返回中文消息
- ✅ 参数验证正常工作
- ✅ 错误处理正确响应
- ✅ 权限验证有效
- ✅ 日志记录完整

### 性能指标

- ⚡ 指令响应时间 < 100ms
- 💾 内存使用 < 50MB
- 🔄 队列处理延迟 < 1s

## 下一步

1. **集成测试** - 与真实 Telegram Bot API 集成
2. **压力测试** - 模拟高并发指令请求
3. **端到端测试** - 完整的用户交互流程
4. **监控告警** - 生产环境监控配置

## 联系支持

如果在测试过程中遇到问题，请检查：

1. 📋 本文档的常见问题部分
2. 📝 项目日志文件
3. 🔧 Docker 容器状态
4. 💾 数据库和 Redis 连接