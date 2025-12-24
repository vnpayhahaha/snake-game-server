<?php

declare(strict_types=1);

/**
 * 语法检查脚本 - 验证PHP文件语法正确性
 */

echo "🔍 PHP Syntax Check for Snake Game Project\n";
echo "📅 " . date('Y-m-d H:i:s') . "\n";
echo str_repeat('=', 50) . "\n";

$filesToCheck = [
    // 核心服务文件
    'app/Service/Telegram/Bot/TelegramCommandService.php',
    'app/Service/Telegram/Bot/CommandEnum.php',
    'app/Service/Player/PlayerWalletBindingService.php',
    'app/Service/Snake/SnakeService.php',
    'app/Repository/Game/GameGroupConfigRepository.php',
    
    // 测试文件
    'tests/Unit/CommandEnumTest.php',
    'tests/Unit/TelegramCommandServiceTest.php',
    
    // 命令文件
    'app/Command/TelegramTestCommand.php',
    'app/Command/InitMockDataCommand.php',
    
    // 测试脚本
    'run_unit_tests.php',
];

$errors = [];
$checked = 0;

foreach ($filesToCheck as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: {$file}\n";
        continue;
    }
    
    echo "Checking: {$file} ... ";
    
    // 使用 php -l 检查语法
    $output = [];
    $returnCode = 0;
    exec("php -l \"{$file}\" 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅\n";
        $checked++;
    } else {
        echo "❌\n";
        $errors[] = [
            'file' => $file,
            'output' => implode("\n", $output)
        ];
    }
}

echo str_repeat('-', 50) . "\n";

if (empty($errors)) {
    echo "🎉 All {$checked} files passed syntax check!\n";
    echo "✅ Ready for container testing\n";
} else {
    echo "❌ Found " . count($errors) . " files with syntax errors:\n\n";
    
    foreach ($errors as $error) {
        echo "File: {$error['file']}\n";
        echo "Error: {$error['output']}\n";
        echo str_repeat('-', 30) . "\n";
    }
}

echo "\n📋 Next Steps:\n";
echo "1. Fix any syntax errors shown above\n";
echo "2. Run tests in Docker container:\n";
echo "   docker exec -it hyper bash\n";
echo "   cd /data/project/snake-game/snake-game-server\n";
echo "   php run_unit_tests.php\n";
echo "\n📅 " . date('Y-m-d H:i:s') . "\n";