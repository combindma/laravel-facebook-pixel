<?php

use Combindma\FacebookPixel\EventLayer;
use Combindma\FacebookPixel\Tests\Models\User;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Auth;

it('can test if config file are set', function () {
    $this->assertEquals('pixel_id', $this->metaPixel->pixelId());
    $this->assertEquals('session_key', $this->metaPixel->sessionKey());
    $this->assertTrue($this->metaPixel->isEnabled());
    $this->assertTrue($this->metaPixel->isAdvancedMatchingEnabled());
});

it('can set and retrieve pixel id', function () {
    $this->metaPixel->setPixelId(123456);
    expect($this->metaPixel->pixelId())->toBe('123456');
});

it('can set and retrieve pixel token', function () {
    $this->metaPixel->setToken('123456ABCDEF');
    expect($this->metaPixel->token())->toBe('123456ABCDEF');
});

it('can retrieve session key', function () {
    $sessionKey = config('meta-pixel.session_key');
    expect($this->metaPixel->sessionKey())->toBe($sessionKey);
});

it('can retrieve token', function () {
    $token = config('meta-pixel.token');
    expect($this->metaPixel->token())->toBe($token);
});

it('can enable and disable pixel on the fly', function () {
    $this->metaPixel->enable();
    expect($this->metaPixel->isEnabled())->toBeTrue();
    $this->metaPixel->disable();
    expect($this->metaPixel->isEnabled())->toBeFalse();
});

it('can track events', function () {
    $this->metaPixel->track('TestEvent', ['param1' => 'value1']);
    $eventLayer = $this->metaPixel->getEventLayer();
    expect($eventLayer)->toBeInstanceOf(EventLayer::class)
        ->and($eventLayer->toArray())->toHaveKey('TestEvent')
        ->and($eventLayer->toArray())->toBe([
            'TestEvent' => [
                'data' => ['param1' => 'value1'],
                'event_id' => null,
            ],
        ]);
});

it('can track events with event id', function () {
    $this->metaPixel->track('TestEvent', ['param1' => 'value1'], 'event-id');
    $eventLayer = $this->metaPixel->getEventLayer();
    expect($eventLayer)->toBeInstanceOf(EventLayer::class)
        ->and($eventLayer->toArray())->toHaveKey('TestEvent')
        ->and($eventLayer->toArray())->toBe([
            'TestEvent' => [
                'data' => ['param1' => 'value1'],
                'event_id' => 'event-id',
            ],
        ]);
});

it('can track custom events', function () {
    $this->metaPixel->trackCustom('CustomEvent', ['customParam' => 'customValue']);
    $customEventLayer = $this->metaPixel->getCustomEventLayer();
    expect($customEventLayer)->toBeInstanceOf(EventLayer::class)
        ->and($customEventLayer->toArray())->toHaveKey('CustomEvent')
        ->and($customEventLayer->toArray())->toBe([
            'CustomEvent' => [
                'data' => ['customParam' => 'customValue'],
                'event_id' => null,
            ],
        ]);
});

it('can track custom events with event id', function () {
    $this->metaPixel->trackCustom('CustomEvent', ['customParam' => 'customValue'], 'event-id');
    $customEventLayer = $this->metaPixel->getCustomEventLayer();
    expect($customEventLayer)->toBeInstanceOf(EventLayer::class)
        ->and($customEventLayer->toArray())->toHaveKey('CustomEvent')
        ->and($customEventLayer->toArray())->toBe([
            'CustomEvent' => [
                'data' => ['customParam' => 'customValue'],
                'event_id' => 'event-id',
            ],
        ]);
});

it('can flash events for the next request', function () {
    $this->metaPixel->flashEvent('FlashEvent', ['flashParam' => 'flashValue']);
    $flashedEvent = $this->metaPixel->getFlashedEvent();
    expect($flashedEvent)->toHaveKey('FlashEvent')
        ->and($flashedEvent)->toBe([
            'FlashEvent' => [
                'data' => ['flashParam' => 'flashValue'],
                'event_id' => null,
            ],
        ]);
});

it('can flash events for the next request with event id', function () {
    $this->metaPixel->flashEvent('FlashEvent', ['flashParam' => 'flashValue'], 'event-id');
    $flashedEvent = $this->metaPixel->getFlashedEvent();
    expect($flashedEvent)->toHaveKey('FlashEvent')
        ->and($flashedEvent)->toBe([
            'FlashEvent' => [
                'data' => ['flashParam' => 'flashValue'],
                'event_id' => 'event-id',
            ],
        ]);
});

it('can merge event session data', function () {
    $eventSession = ['MergedEvent' => ['mergedParam' => 'mergedValue']];
    $this->metaPixel->merge($eventSession);
    $eventLayer = $this->metaPixel->getEventLayer();
    expect($eventLayer->toArray())->toHaveKey('MergedEvent');
});

it('can clear the event layer', function () {
    $this->metaPixel->track('TestEvent', ['param1' => 'value1']);
    $this->metaPixel->clear();
    $eventLayer = $this->metaPixel->getEventLayer();
    expect($eventLayer->toArray())->toBeEmpty();
});

it('can retrieve user data for advanced matching', function () {
    $user = new User(['id' => 12345, 'email' => 'test@example.com']);
    // Mock the Auth facade to return the user
    Auth::shouldReceive('check')
        ->once()
        ->andReturn(true);
    Auth::shouldReceive('user')
        ->twice()
        ->andReturn($user);

    expect($this->metaPixel->getUser())->toBe([
        'em' => 'test@example.com',
        'external_id' => 12345,
    ]);
});

it('returns null when no user is authenticated for getUser', function () {
    Auth::shouldReceive('check')
        ->once()
        ->andReturn(false);

    expect($this->metaPixel->getUser())->toBeNull();
});

it('send method returns null when pixel is disabled', function () {
    $this->metaPixel->disable();

    $eventName = 'TestEvent';
    $eventId = 'EVENT_ID';
    $userData = new UserData;
    $customData = new CustomData;

    expect($this->metaPixel->send($eventName, $eventId, $customData, $userData))->toBeNull();
});

it('throws an exception when token is not set', function () {
    // Mock the configuration to return an empty token
    config()->set('facebook-pixel.token', '');

    $eventName = 'TestEvent';
    $eventId = 'EVENT_ID';
    $customData = new CustomData;

    // Expect an Exception with a specific message
    expect(fn () => $this->metaPixel->send($eventName, $eventId, $customData))->toThrow(Exception::class);
});
