<?php

declare(strict_types=1);

namespace Services\Telegram;

use Throwable;
use Illuminate\Support\Facades\Http;
use Services\Telegram\TelegramBotApiFake;
use Services\Telegram\TelegramBotApiContract;
use Services\Telegram\Exceptions\TelegramBotApiException;

class TelegramBotApi implements TelegramBotApiContract
{
    public const HOST = 'https://api.telegram.org/bot';

    public static function fake(): TelegramBotApiFake
    {
        return app()->instance(
            TelegramBotApiContract::class,
            new TelegramBotApiFake()
        );
    }

    public static function sendMessage(string $token, int $chat_id, string $text): bool
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
