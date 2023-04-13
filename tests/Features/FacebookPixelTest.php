<?php

use Combindma\FacebookPixel\EventLayer;
use Combindma\FacebookPixel\FacebookPixel;
use Combindma\FacebookPixel\Tests\Models\User;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->facebookPixel = new FacebookPixel();
});

it('can test if config file are set', function () {
    $this->assertEquals('facebook_pixel_id', $this->facebookPixel->pixelId());
    $this->assertEquals('sessionKey', $this->facebookPixel->sessionKey());
    $this->assertTrue($this->facebookPixel->isEnabled());
});

it('can set and retrieve pixel id', function () {
    $this->facebookPixel->setPixelId(123456);
    expect($this->facebookPixel->pixelId())->toBe('123456');
});

it('can retrieve session key', function () {
    $sessionKey = config('facebook-pixel.sessionKey');
    expect($this->facebookPixel->sessionKey())->toBe($sessionKey);
});

it('can retrieve token', function () {
    $token = config('facebook-pixel.token');
    expect($this->facebookPixel->token())->toBe($token);
});

it('can enable and disable pixel on the fly', function () {
    $this->facebookPixel->enable();
    expect($this->facebookPixel->isEnabled())->toBeTrue();
    $this->facebookPixel->disable();
    expect($this->facebookPixel->isEnabled())->toBeFalse();
});

it('can track events', function () {
    $this->facebookPixel->track('TestEvent', ['param1' => 'value1']);
    $eventLayer = $this->facebookPixel->getEventLayer();
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
    $this->facebookPixel->track('TestEvent', ['param1' => 'value1'], 'event-id');
    $eventLayer = $this->facebookPixel->getEventLayer();
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
    $this->facebookPixel->trackCustom('CustomEvent', ['customParam' => 'customValue']);
    $customEventLayer = $this->facebookPixel->getCustomEventLayer();
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
    $this->facebookPixel->trackCustom('CustomEvent', ['customParam' => 'customValue'], 'event-id');
    $customEventLayer = $this->facebookPixel->getCustomEventLayer();
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
    $this->facebookPixel->flashEvent('FlashEvent', ['flashParam' => 'flashValue']);
    $flashedEvent = $this->facebookPixel->getFlashedEvent();
    expect($flashedEvent)->toHaveKey('FlashEvent')
        ->and($flashedEvent)->toBe([
            'FlashEvent' => [
                'data' => ['flashParam' => 'flashValue'],
                'event_id' => null,
            ],
        ]);
});

it('can flash events for the next request with event id', function () {
    $this->facebookPixel->flashEvent('FlashEvent', ['flashParam' => 'flashValue'], 'event-id');
    $flashedEvent = $this->facebookPixel->getFlashedEvent();
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
    $this->facebookPixel->merge($eventSession);
    $eventLayer = $this->facebookPixel->getEventLayer();
    expect($eventLayer->toArray())->toHaveKey('MergedEvent');
});

it('can clear the event layer', function () {
    $this->facebookPixel->track('TestEvent', ['param1' => 'value1']);
    $this->facebookPixel->clear();
    $eventLayer = $this->facebookPixel->getEventLayer();
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

    expect($this->facebookPixel->getUser())->toBe([
        'em' => 'test@example.com',
        'external_id' => 12345,
    ]);
});

it('returns null when no user is authenticated for getUser', function () {
    Auth::shouldReceive('check')
        ->once()
        ->andReturn(false);

    expect($this->facebookPixel->getUser())->toBeNull();
});

it('send method returns null when pixel is disabled', function () {
    $this->facebookPixel->disable();

    $eventName = 'TestEvent';
    $eventId = 'EVENT_ID';
    $userData = new UserData();
    $customData = new CustomData();

    expect($this->facebookPixel->send($eventName, $eventId, $customData, $userData))->toBeNull();
});

it('throws an exception when token is not set', function () {
    // Mock the configuration to return an empty token
    config()->set('facebook-pixel.token', '');

    $eventName = 'TestEvent';
    $eventId = 'EVENT_ID';
    $customData = new CustomData();

    // Expect an Exception with a specific message
    expect(fn () => $this->facebookPixel->send($eventName, $eventId, $customData))->toThrow(Exception::class);
});
