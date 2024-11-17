<?php

namespace Combindma\FacebookPixel\Facades;

use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Combindma\FacebookPixel\MetaPixel
 *
 * @method static pixelId()
 * @method static setPixelId(int|string $id)
 * @method static setToken(string $token)
 * @method static enable()
 * @method static disable()
 * @method static clear()
 * @method static track(string $eventName, array $parameters = [], string $eventId = null)
 * @method static trackCustom(string $eventName, array $parameters = [], string $eventId = null)
 * @method static flashEvent(string $eventName, array $parameters = [], string $eventId = null)
 * @method static userData()
 * @method static send(string $eventName, string $eventId, CustomData $customData, UserData $userData = null)
 */
class MetaPixel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Combindma\FacebookPixel\MetaPixel::class;
    }
}
