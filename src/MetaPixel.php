<?php

namespace Combindma\FacebookPixel;

use Exception;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\EventResponse;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class MetaPixel
{
    use Macroable;

    private bool $enabled;

    private bool $logEnabled;

    private bool $advancedMatchingEnabled;

    private string $pixelId;

    private ?string $token;

    private ?string $appSecret;

    private string $sessionKey;

    private ?string $testEventCode;

    private EventLayer $eventLayer;

    private EventLayer $customEventLayer;

    private EventLayer $flashEventLayer;

    public function __construct()
    {
        $this->enabled = config('meta-pixel.enabled');
        $this->logEnabled = config('meta-pixel.logging');
        $this->advancedMatchingEnabled = config('meta-pixel.advanced_matching_enabled');
        $this->pixelId = config('meta-pixel.pixel_id');
        $this->token = config('meta-pixel.token');
        $this->appSecret = config('meta-pixel.app_secret');
        $this->sessionKey = config('meta-pixel.session_key');
        $this->testEventCode = config('meta-pixel.test_event_code');
        $this->eventLayer = new EventLayer;
        $this->customEventLayer = new EventLayer;
        $this->flashEventLayer = new EventLayer;
    }

    public function pixelId(): string
    {
        return (string) $this->pixelId;
    }

    public function sessionKey(): string
    {
        return $this->sessionKey;
    }

    public function token(): ?string
    {
        return $this->token;
    }

    public function testEnabled(): bool
    {
        return (bool) $this->testEventCode;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isAdvancedMatchingEnabled(): bool
    {
        return $this->advancedMatchingEnabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function setPixelId(int|string $id): void
    {
        $this->pixelId = (string) $id;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function setTestEventCode(string $code): void
    {
        $this->testEventCode = $code;
    }

    /**
     * Add event to the event layer.
     */
    public function track(string $eventName, array $parameters = [], ?string $eventId = null): void
    {
        $this->eventLayer->set($eventName, $parameters, $eventId);
    }

    /**
     * Add custom event to the event layer.
     */
    public function trackCustom(string $eventName, array $parameters = [], ?string $eventId = null): void
    {
        $this->customEventLayer->set($eventName, $parameters, $eventId);
    }

    /**
     * Add event data to the event layer for the next request.
     */
    public function flashEvent(string $eventName, array $parameters = [], ?string $eventId = null): void
    {
        $this->flashEventLayer->set($eventName, $parameters, $eventId);
    }

    /**
     * Track a browser event and send the matching Conversions API event with the same event ID.
     */
    public function trackAndSend(
        string $eventName,
        array $parameters,
        CustomData $customData,
        ?UserData $userData = null,
        ?string $eventId = null,
    ): ?EventResponse {
        $resolvedEventId = $eventId ?? $this->generateEventId();

        $this->track($eventName, $parameters, $resolvedEventId);

        return $this->send($eventName, $resolvedEventId, $customData, $userData);
    }

    public function userData(): UserData
    {
        $userData = (new UserData)
            ->setClientIpAddress(Request::ip())
            ->setClientUserAgent(Request::userAgent())
            ->setFbc(Arr::get($_COOKIE, '_fbc'))
            ->setFbp(Arr::get($_COOKIE, '_fbp'));

        $user = $this->getUser();

        if ($user === null) {
            return $userData;
        }

        return $userData
            ->setEmail($user['em'])
            ->setExternalId($user['external_id']);
    }

    /**
     * Send request using Conversions API
     */
    public function send(string $eventName, string $eventID, CustomData $customData, ?UserData $userData = null): ?EventResponse
    {
        if (! $this->isEnabled()) {
            return null;
        }

        if (empty($this->token())) {
            throw new Exception('You need to set a token in your .env file to use the Conversions API.');
        }

        $api = Api::init(null, $this->appSecret ?? null, $this->token());

        if ($this->logEnabled) {
            $api->setLogger(new CurlLogger);
        }

        $event = (new Event)
            ->setEventName($eventName)
            ->setEventTime(time())
            ->setEventId($eventID)
            ->setEventSourceUrl(URL::current())
            ->setUserData($userData ?? $this->userData())
            ->setCustomData($customData)
            ->setActionSource(ActionSource::WEBSITE);

        $request = (new EventRequest($this->pixelId()))->setEvents([$event]);

        if ($this->testEnabled()) {
            $request->setTestEventCode($this->testEventCode);
        }

        try {
            return $request->execute();
        } catch (Exception $e) {
            if ($this->logEnabled) {
                Log::error($e);
            }
        }

        return null;
    }

    /**
     * Merge array data with the event layer.
     */
    public function merge(array $eventSession): void
    {
        $this->eventLayer->merge($eventSession);
    }

    /**
     * Retrieve the event layer.
     */
    public function getEventLayer(): EventLayer
    {
        return $this->eventLayer;
    }

    /**
     * Retrieve custom event layer.
     */
    public function getCustomEventLayer(): EventLayer
    {
        return $this->customEventLayer;
    }

    /**
     * Retrieve the event layer's data for the next request.
     */
    public function getFlashedEvent(): array
    {
        return $this->flashEventLayer->toArray();
    }

    /**
     * @return array{em: string, external_id: string}|null
     */
    public function getUser(): ?array
    {
        if ($this->isAdvancedMatchingEnabled() && Auth::check()) {
            $user = Auth::user();
            $email = data_get($user, 'email');
            $userId = data_get($user, 'id');

            if (! is_string($email) || $email === '' || $userId === null) {
                return null;
            }

            return [
                'em' => strtolower($email),
                'external_id' => (string) $userId,
            ];
        }

        return null;
    }

    public function clear(): void
    {
        $this->eventLayer = new EventLayer;
    }

    protected function generateEventId(): string
    {
        return (string) Str::uuid();
    }
}
