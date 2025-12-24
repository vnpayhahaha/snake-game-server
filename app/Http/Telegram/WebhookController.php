<?php

declare(strict_types=1);

namespace App\Http\Telegram;

use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Service\Telegram\Bot\TelegramService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

#[Controller]
class WebhookController extends AbstractController
{
    #[Inject]
    protected TelegramService $telegramService;

    #[RequestMapping(path: "/telegram/notify", methods: "POST")]
    public function notify(): Result
    {
        $url = env('APP_DOMAIN', 'https://server.yypay.cloud') . '/v1/common/telegram/webHook';
        return $this->success($this->telegramService->notify($url));
    }

    #[RequestMapping(path: "/telegram/webhook", methods: "POST")]
    public function index(RequestInterface $request): string
    {
        $this->telegramService->webHook($request->all());
        return 'ok';
    }
}
