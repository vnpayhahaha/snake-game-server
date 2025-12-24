<?php

/**
 * 简单的测试运行脚本
 * 用于验证测试环境是否正常工作
 */

echo "🚀 Starting Test Environment Check\n";
echo "📅 " . date('Y-m-d H:i:s') . "\n";
echo str_repeat('=', 60) . "\n";

// 检查 PHP 版本
echo "📋 PHP Version: " . PHP_VERSION . "\n";

// 检查必要的扩展
$requiredExtensions = ['pdo', 'redis', 'json', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "📦 Extension {$ext}: {$status}\n";
}

// 检查 Hyperf 是否可用
try {
    if (class_exists('Hyperf\Testing\TestCase')) {
        echo "✅ Hyperf Testing Framework: Available\n";
    } else {
        echo "❌ Hyperf Testing Framework: Not Available\n";
    }
} catch (Exception $e) {
    echo "❌ Hyperf Testing Framework: Error - " . $e->getMessage() . "\n";
}

// 检查测试文件
$testFiles = [
    'tests/Unit/CommandEnumTest.php',
    'tests/Unit/TelegramCommandServiceTest.php',
    'tests/Feature/TelegramBotIntegrationTest.php',
    'tests/Feature/TelegramBotPerformanceTest.php',
];

echo "\n📁 Test Files Check:\n";
foreach ($testFiles as $file) {
    $exists = file_exists($file);
    $status = $exists ? '✅' : '❌';
    echo "   {$status} {$file}\n";
    
    if ($exists) {
        $content = file_get_contents($file);
        $hasExpectFunction = strpos($content, 'expect(') !== false;
        $hasPestSyntax = strpos($content, 'describe(') !== false || strpos($content, 'it(') !== false;
        
        if ($hasPestSyntax) {
            echo "      ⚠️  Contains Pest syntax (needs conversion)\n";
        } elseif ($hasExpectFunction) {
            echo "      ⚠️  Contains expect() function (may need PHPUnit conversion)\n";
        } else {
            echo "      ✅ Standard PHPUnit format\n";
        }
    }
}

// 检查核心类文件
$coreFiles = [
    'app/Service/Telegram/Bot/TelegramCommandService.php',
    'app/Service/Telegram/Bot/CommandEnum.php',
    'app/Service/Player/PlayerWalletBindingService.php',
    'app/Service/Snake/SnakeService.php',
];

echo "\n📁 Core Files Check:\n";
foreach ($coreFiles as $file) {
    $exists = file_exists($file);
    $status = $exists ? '✅' : '❌';
    echo "   {$status} {$file}\n";
}

// 检查 PHPUnit 配置
echo "\n⚙️  PHPUnit Configuration:\n";
if (file_exists('phpunit.xml.dist')) {
    echo "   ✅ phpunit.xml.dist exists\n";
    $config = file_get_contents('phpunit.xml.dist');
    if (strpos($config, 'bootstrap="./tests/bootstrap.php"') !== false) {
        echo "   ✅ Bootstrap configured\n";
    } else {
        echo "   ⚠️  Bootstrap may not be configured correctly\n";
    }
} else {
    echo "   ❌ phpunit.xml.dist not found\n";
}

if (file_exists('tests/bootstrap.php')) {
    echo "   ✅ tests/bootstrap.php exists\n";
} else {
    echo "   ❌ tests/bootstrap.php not found\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "🎉 Test Environment Check Completed!\n";
echo "📅 " . date('Y-m-d H:i:s') . "\n";

// 尝试运行一个简单的测试
echo "\n🧪 Running Simple Test:\n";
try {
    require_once 'app/Service/Telegram/Bot/CommandEnum.php';
    
    $testCommand = 'start';
    $isValid = \App\Service\Telegram\Bot\CommandEnum::isCommand($testCommand);
    $method = \App\Service\Telegram\Bot\CommandEnum::getCommand($testCommand);
    
    echo "   Testing command: '{$testCommand}'\n";
    echo "   Is valid: " . ($isValid ? 'Yes' : 'No') . "\n";
    echo "   Method: '{$method}'\n";
    
    if ($isValid && $method === 'Start') {
        echo "   ✅ CommandEnum basic test passed\n";
    } else {
        echo "   ❌ CommandEnum basic test failed\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Simple test failed: " . $e->getMessage() . "\n";
}

echo "\n💡 Next Steps:\n";
echo "   1. Run: composer test (or vendor/bin/phpunit)\n";
echo "   2. Run: php bin/hyperf.php telegram:test\n";
echo "   3. Check individual test files if needed\n";