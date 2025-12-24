<?php

namespace App\Service\Telegram\Bot;

use App\Repository\Telegram\TelegramCommandMessageRecordRepository;
use Hyperf\Redis\Redis;
use Hyperf\Di\Annotation\Inject;
use JsonException;
use Telegram as TelegramBot;


class TelegramService
{
    private TelegramBot $telegramBot;

    #[Inject]
    protected TelegramCommandService $telegramCommandService;

    #[Inject]
    protected TelegramCommandMessageRecordRepository  $telegramCommandMessageRecordRepository;

    #[Inject]
    private Redis $redis;

    public function __construct()
    {
        logger()->debug('初始化 TELEGRAM_TOKEN', ['token' => env('TELEGRAM_TOKEN')]);
        $this->telegramBot = new TelegramBot(env('TELEGRAM_TOKEN'));
    }

    public function notify($url)
    {
        return $this->telegramBot->setWebhook($url);
    }


    // 监听webHook消息
    public function webHook(array $params): bool
    {
        logger()->debug('收到 webHook 消息', ['params' => $params]);
        $this->telegramBot->setData($params);
        $this->telegramCommandService->setTelegramBot($this->telegramBot);
        //把信息进行分类，分开是私人聊天还是群内聊天
        $is_group = $this->telegramBot->messageFromGroup();
        logger()->debug('webHook 消息类型', ['is_group' => $is_group]);
        if ($is_group) {
            $chat_id = (int)$this->telegramBot->ChatID();
            try {
                $this->groupWork();
            } catch (\Throwable $e) {
                return $this->sendMessageProducer($chat_id, $e->getMessage(), $this->telegramBot->MessageID());
            }
        } else {
            $this->privateWork();
        }
        return true;
    }


    public function groupWork(): bool
    {
        $text = $this->telegramBot->Text();
        $chat_id = (int)$this->telegramBot->ChatID();
        $type = $this->telegramBot->getUpdateType();
        logger()->debug('处理群组消息', ['type' => $type, 'text' => $text, 'chat_id' => $chat_id]);

        // 如果 text 是 / 开头的，则尝试查询telegramCommandService对应的方法，空格后面的参数会作为参数传入telegramCommandService对应的方法
        // eg: /bind daxiong 18 185cm  $this->telegramCommandService->bind(['daxiong','18','185cm'])
        if ($type === TelegramBot::MESSAGE && filled($text) && str_starts_with($text, '/')) {
            try {
                $commandOriginal = substr($text, 1);
                $params = explode('@', $commandOriginal);
                logger()->debug('尝试解析命令', ['separator' => '@', 'params' => $params]);
                if ($this->commandRunProducer($params)) {
                    return true;
                }
                // 采用 PHP_EOL 换行符分割
                $params = explode(PHP_EOL, $commandOriginal);
                logger()->debug('尝试解析命令', ['separator' => '换行符', 'params' => $params]);
                if ($this->commandRunProducer($params)) {
                    return true;
                }
                $params = explode(' ', $commandOriginal);
                logger()->debug('尝试解析命令', ['separator' => '空格', 'params' => $params]);
                if ($this->commandRunProducer($params)) {
                    return true;
                }
            } catch (\Throwable $e) {
                return $this->sendMessageProducer($chat_id, [
                    'Execute command exception:',
                    $e->getMessage(),
                ], $this->telegramBot->MessageID());
            }
            return $this->sendMessageProducer($chat_id, [
                'Unknown commands, you can obtain command information through /help!',
                '未知指令,可通过[/帮助]获取指令信息!',
            ], $this->telegramBot->MessageID());
        }

        // 其他类型的消息（图片、视频等）暂不处理
        return false;
    }

    private function commandRunProducer(array $params): bool
    {
        $firstParam = array_shift($params);
        $command = strtolower(trim($firstParam));
        if (!CommandEnum::isCommand($command)) {
            return false;
        }
        $method = CommandEnum::getCommand($command);
        logger()->debug('识别到命令', ['command' => $command, 'method' => $method]);
        // 过滤空并重置索引
        $params = array_filter($params);
        // trim
        $params = array_map('trim', array_values($params));
        if (method_exists($this->telegramCommandService, $method)) {
            $data = [
                'data'    => $this->telegramBot->getData(),
                'params'  => $params,
                'method'  => $method,
                'command' => $command,
            ];
            return $this->redis->lpush(CommandEnum::TELEGRAM_COMMAND_RUN_QUEUE_NAME, json_encode($data));
        }
        return false;
    }

    public function commandRunConsumer(array $data): bool
    {
        if (!isset($data['data'], $data['params'], $data['method'], $data['command'])) {
            logger()->error('命令消费者参数错误', ['data' => $data]);
            return false;
        }
        $this->telegramBot->setData($data['data']);
        $this->telegramCommandService->setTelegramBot($this->telegramBot);
        $record = $this->telegramCommandMessageRecordRepository->getModel()->firstOrCreate([
            'chat_id'    => $this->telegramBot->ChatID(),
            'message_id' => $this->telegramBot->MessageID(),
        ], [
            'command'          => $data['command'],
            'chat_name'        => $this->telegramBot->getGroupTitle(),
            'user_id'          => $this->telegramBot->UserId(),
            'username'         => $this->telegramBot->UserName() ?? '',
            'nickname'         => $this->telegramBot->FirstName() . ' ' . $this->telegramBot->LastName(),
            'original_message' => $this->telegramBot->Text(),
        ]);
        try {
            $result = $this->telegramCommandService->{$data['method']}($this->telegramBot->UserId(), $data['params'], $record->id);
        } catch (\Throwable $e) {
            logger()->error('命令执行异常', [
                'method' => $data['method'],
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return $this->returnException($this->telegramBot->ChatID(), $e, $record->id);
        }
        return $this->sendMessageProducer($this->telegramBot->ChatID(), $result, $this->telegramBot->MessageID());
    }

    public function privateWork(): void
    {
        $text = $this->telegramBot->Text();
        $chat_id = (int)$this->telegramBot->ChatID();
        $type = $this->telegramBot->getUpdateType();

        if ($text === '/start') {
            try {
                $list[] = '🐍 Welcome to Snake Chain Game!';
                $list[] = '欢迎来到 Snake Chain Game！';
                $list[] = '';
                $list[] = 'Nickname：<code>' . $this->telegramBot->FirstName() . ' ' . $this->telegramBot->LastName() . '</code>';
                $list[] = 'Username：<code>' . $this->telegramBot->UserName() . '</code>';
                $list[] = 'UserID：<code>' . $this->telegramBot->UserId() . '</code>';
                $list[] = '';
                $list[] = '💡 Join a game group to start playing!';
                $list[] = '加入游戏群组开始游戏！';

                $this->sendMessageProducer($chat_id, $list);
                return;
            } catch (\Exception $e) {
                $this->returnException($chat_id, $e);
            }
        }

        // 私聊中的其他指令响应
        $this->sendMessageProducer($chat_id, [
            '⚠️ This bot works in group chats only',
            '此机器人仅在群组聊天中工作',
            '',
            'Please add me to a game group and use commands there.',
            '请将我添加到游戏群组并在那里使用指令。',
        ]);
    }

    /**格式化文字
     * @param array $array
     * @return string
     */
    public static function formatTxt(array $array): string
    {
        $text = '';
        foreach ($array as $item) {
            if ($text === '') {
                $text = $item;
            } else {
                $text .= PHP_EOL . $item;
            }
        }
        return $text;
    }

    /**群内回复异常
     * @param $chat_id
     * @param $e
     * @param $token
     * @return void
     */
    public function returnException($chat_id, $e, $recordID = 0): bool
    {
        $reply = 'Exception info：' . PHP_EOL . $e->getMessage() . PHP_EOL . 'LINE:' . $e->getLine() . PHP_EOL . 'Trace:' . PHP_EOL . $e->getTraceAsString();
        if ($recordID > 0) {
            $this->telegramCommandMessageRecordRepository->getModel()->where([
                'chat_id'    => $chat_id,
                'message_id' => $this->telegramBot->MessageID(),
            ])->update([
                'response_message' => $reply,
                'status'           => 3
            ]);
        }
        return $this->sendMessageProducer($chat_id, $reply);
    }

    /**
     * @param int $chat_id
     * @param mixed $content
     * @param int $reply_markup
     * @return bool
     * @throws JsonException
     */
    public function sendMessageProducer(int $chat_id, mixed $content, int $reply_markup = 0): bool
    {
        if (is_array($content)) {
            $content = self::formatTxt($content);
        } else if (is_string($content)) {
            $content = trim($content);
        } else if (is_numeric($content)) {
            $content = (string)$content;
        } else if (is_object($content)) {
            $content = json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } else if (is_bool($content)) {
            $content = $content ? 'successful' : 'failed';
        } else {
            return false;
        }
        $data = array(
            'chat_id'    => $chat_id,
            'text'       => $content,
            'parse_mode' => 'HTML'
        );
        if ($reply_markup > 0) {
            $data['reply_to_message_id'] = $reply_markup;
        }
        return $this->redis->lpush(CommandEnum::TELEGRAM_NOTICE_QUEUE_NAME, json_encode($data));
    }

    /**
     * @param array $data
     *  - string int
     *  - string text
     *  - string parse_mode HTML
     */
    public function sendMessageConsumer(array $data)
    {
        $message_id = $data['reply_to_message_id'] ?? 0;
        if ($message_id > 0) {
            $data['text'] = '[Reply|回复]' . PHP_EOL . $data['text'];
            $this->telegramCommandMessageRecordRepository->getModel()->where([
                'chat_id'    => $data['chat_id'],
                'message_id' => $message_id,
            ])->update([
                'response_message' => $data['text'],
                'status'           => 2
            ]);
        }

        return $this->telegramBot->sendMessage($data);
    }
}