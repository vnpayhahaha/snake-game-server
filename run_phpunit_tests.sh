#!/bin/bash

# PHPUnit测试运行脚本 - 专门用于Docker容器环境
# 只运行 tests/Unit 下的业务相关测试

echo "🧪 Running PHPUnit Tests for Business Logic"
echo "📅 $(date '+%Y-%m-%d %H:%M:%S')"
echo "🎯 Target: tests/Unit/ directory"
echo "============================================================"

# 检查PHPUnit是否存在
if [ ! -f "./vendor/bin/co-phpunit" ]; then
    echo "❌ co-phpunit not found. Installing dependencies..."
    composer install --no-dev
    if [ $? -ne 0 ]; then
        echo "❌ Failed to install dependencies"
        exit 1
    fi
fi

# 检查测试目录是否存在
if [ ! -d "./tests/Unit" ]; then
    echo "❌ tests/Unit directory not found"
    exit 1
fi

echo "✅ Environment check passed"
echo ""

# 运行Unit测试
echo "🚀 Running Unit Tests..."
echo "------------------------------------------------------------"

# 使用co-phpunit运行测试，只测试Unit目录
./vendor/bin/co-phpunit \
    --prepend tests/bootstrap.php \
    --colors=always \
    --verbose \
    --stop-on-failure \
    tests/Unit/

TEST_RESULT=$?

echo ""
echo "============================================================"

if [ $TEST_RESULT -eq 0 ]; then
    echo "🎉 All Unit Tests Passed!"
    echo "✅ Business logic is working correctly"
    echo ""
    echo "📋 Next Steps:"
    echo "1. Run integration tests: php bin/hyperf.php telegram:test"
    echo "2. Test with real data: php bin/hyperf.php telegram:test --user-id=123456789"
    echo "3. Check logs for any warnings or notices"
else
    echo "❌ Unit Tests Failed!"
    echo "🔧 Please check the error messages above and fix the issues"
    echo ""
    echo "📋 Common Issues and Solutions:"
    echo "1. Syntax errors: Check PHP syntax in test files"
    echo "2. Missing dependencies: Run 'composer install'"
    echo "3. Database issues: Check .env configuration"
    echo "4. Class not found: Check autoload and namespaces"
    echo ""
    echo "🔍 For detailed debugging, run:"
    echo "   php run_business_unit_tests.php"
fi

echo "📅 Test completed: $(date '+%Y-%m-%d %H:%M:%S')"
exit $TEST_RESULT