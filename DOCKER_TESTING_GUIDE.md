# Docker容器测试指南

## 测试环境说明

本项目的测试需要在Docker容器中进行，因为需要完整的Hyperf框架环境和相关依赖。

## 容器信息

- **容器名称**: hyper
- **项目路径**: `/data/project/snake-game/snake-game-server`
- **PHP版本**: 8.3+
- **框架**: Hyperf 3.1

## 测试方法

### 方法1: 使用简化的单元测试脚本（推荐）

```bash
# 进入容器
docker exec -it hyper bash

# 进入项目目录
cd /data/project/snake-game/snake-game-server

# 运行简化的单元测试
php run_unit_tests.php
```

### 方法2: 使用Hyperf测试命令

```bash
# 进入容器
docker exec -it hyper bash

# 进入项目目录
cd /data/project/snake-game/snake-game-server

# 运行Telegram测试命令
php bin/hyperf.php telegram:test

# 运行特定指令测试
php bin/hyperf.php telegram:test start
php bin/hyperf.php telegram:test 开始

# 运行中文指令测试
php bin/hyperf.php telegram:test --lang=cn

# 运行英文指令测试
php bin/hyperf.php telegram:test --lang=en
```

### 方法3: 使用标准PHPUnit（如果环境支持）

```bash
# 进入容器
docker exec -it hyper bash

# 进入项目目录
cd /data/project/snake-game/snake-game-server

# 运行单元测试
./vendor/bin/co-phpunit tests/Unit/ --colors=always

# 或者使用composer脚本
composer test
```

## 测试内容

### Unit测试覆盖范围

1. **CommandEnum测试**
   - 英文指令映射正确性
   - 中文指令映射正确性
   - 大小写不敏感功能
   - 无效指令拒绝
   - 帮助信息生成
   - 指令描述完整性
   - 方法命名规范

2. **TelegramCommandService测试**
   - 基础指令功能（Start, Help, Rules等）
   - 中英文版本对应
   - 参数验证逻辑
   - 方法签名一致性
   - 错误处理机制

### 测试特性

- ✅ **大小写不敏感**: 支持 `/start`, `/START`, `/Start` 等格式
- ✅ **中英文适配**: 英文指令返回英文消息，中文指令返回中文消息
- ✅ **参数验证**: 钱包地址格式验证、参数完整性检查
- ✅ **错误处理**: 友好的错误提示信息
- ✅ **方法规范**: 统一的方法签名 `(int $userId, array $params, int $recordID): array`

## 预期测试结果

### 成功输出示例

```
🚀 Starting Unit Tests for Snake Game Telegram Bot
📅 2025-01-08 10:30:00
============================================================

✅ Hyperf framework initialized successfully

📋 Running Unit Tests:
----------------------------------------

🧪 Testing CommandEnum...
  ✅ English commands mapping test passed
  ✅ Chinese commands mapping test passed
  ✅ Case insensitive test passed
  ✅ Invalid commands rejection test passed
  ✅ Help messages test passed
✅ CommandEnum tests completed successfully

🧪 Testing TelegramCommandService...
  ✅ TelegramCommandService instance created
  ✅ Basic commands test passed
  ✅ Parameter validation test passed
  ✅ Method signature consistency test passed
✅ TelegramCommandService tests completed successfully

============================================================
🎉 All Unit Tests Completed!
📅 2025-01-08 10:30:05
```

## 故障排除

### 常见问题

1. **"Call to undefined function describe()" 错误**
   - 原因: 使用了Pest语法但环境不支持
   - 解决: 使用 `run_unit_tests.php` 脚本，已转换为标准PHP代码

2. **"Class not found" 错误**
   - 原因: 自动加载问题
   - 解决: 确保在容器中运行，并且已执行 `composer install`

3. **数据库连接错误**
   - 原因: 数据库服务未启动或配置错误
   - 解决: 检查 `.env` 配置，确保数据库服务正常

4. **Redis连接错误**
   - 原因: Redis服务未启动
   - 解决: 启动Redis服务或跳过需要Redis的测试

### 调试技巧

1. **查看详细错误信息**
   ```bash
   php run_unit_tests.php 2>&1 | tee test_output.log
   ```

2. **检查Hyperf服务状态**
   ```bash
   php bin/hyperf.php start
   ```

3. **验证依赖安装**
   ```bash
   composer install --no-dev
   composer dump-autoload
   ```

## 测试报告

测试完成后，请检查以下指标：

- [ ] CommandEnum所有测试通过
- [ ] TelegramCommandService所有测试通过
- [ ] 大小写不敏感功能正常
- [ ] 中英文适配正确
- [ ] 参数验证逻辑正确
- [ ] 错误处理友好

## 下一步

测试通过后，可以进行：

1. **功能测试**: 使用 `php bin/hyperf.php telegram:test` 进行完整功能测试
2. **集成测试**: 测试与数据库、Redis的集成
3. **性能测试**: 测试高并发场景下的表现
4. **实际部署**: 部署到生产环境进行真实测试