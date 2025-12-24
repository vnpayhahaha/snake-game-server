#!/bin/bash

echo "🐳 Running tests in Docker container..."
echo "📅 $(date)"
echo "========================================"

# 检查 Docker 容器是否运行
if ! docker ps | grep -q "hyper"; then
    echo "❌ Docker container 'hyper' is not running"
    echo "Please start the container first:"
    echo "docker start hyper"
    exit 1
fi

echo "✅ Docker container 'hyper' is running"

# 进入容器并运行测试环境检查
echo ""
echo "🔍 Running test environment check..."
docker exec hyper php /data/project/snake-game/snake-game-server/run_tests.php

echo ""
echo "🧪 Running PHPUnit tests..."
docker exec hyper bash -c "cd /data/project/snake-game/snake-game-server && composer test"

echo ""
echo "🎯 Running Hyperf telegram test command..."
docker exec hyper bash -c "cd /data/project/snake-game/snake-game-server && php bin/hyperf.php telegram:test"

echo ""
echo "📊 Test execution completed!"
echo "📅 $(date)"