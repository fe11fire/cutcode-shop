<?php

declare(strict_types=1);

namespace Services\Telegram;

use Services\Telegram\Exceptions\TelegramBotApiException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

class TelegramBotApi
{
    public const HOST = 'https://api.telegram.org/bot';

    public static function sendMessage(string $token, int $chat_id, string $text)
    {
        try {
            $res = Http::get(self::HOST . $token . '/sendMessage', [
                'chat_id' => $chat_id,
                'text' => $text,
            ])->throw();
            return $res->successful();
        } catch (Throwable $e) {
            report(new TelegramBotApiException($e->getMessage()));
            return false;
        }
    }
}
