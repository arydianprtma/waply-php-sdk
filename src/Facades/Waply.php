<?php

namespace Waply\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array sendText(string $to, string $message, ?string $deviceId = null)
 * @method static array sendTemplate(string $to, string $template, array $variables = [], ?string $deviceId = null)
 * @method static array sendMedia(string $to, string $message, string $mediaUrl, string $mediaType = 'image', string $fileName = '', ?string $deviceId = null)
 * @method static array createBroadcast(string $title, string $template, array $recipients, int $batchSize = 10, int $batchDelaySec = 60)
 * @method static array getSessions()
 *
 * @see \Waply\WaplyClient
 */
class Waply extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'waply';
    }
}
