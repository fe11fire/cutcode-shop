<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class TelegramBotApi
{
    public const HOST = 'https://api.telegram.org/bot';
    public static function sendMessage(string $token, int $chat_id, string $text)
    {
        try {
            $res = Http::get(self::HOST . $token . '/sendMessage', [
                'chat_id' => $chat_id,
                'text' => $text,
            ]);
            return $res->successful();
        } catch (Exception $e) {
            return false;
        }
    }
}
