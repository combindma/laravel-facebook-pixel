<?php

use Combindma\FacebookPixel\MetaPixel;
use Combindma\FacebookPixel\MetaPixelMiddleware;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

beforeEach(function () {
    // Mock the MetaPixel class
    $this->metaPixel = Mockery::mock(MetaPixel::class);
    $this->session = app(SessionManager::class)->driver('array');
    $this->middleware = new MetaPixelMiddleware($this->metaPixel, $this->session);
});

test('it merges session data with the MetaPixel event layer', function () {
    $this->session->put('pixelSessionKey', ['some_event' => ['data' => ['param1' => 'value1'], 'event_id' => null]]);
    $this->metaPixel->shouldReceive('sessionKey')->andReturn('pixelSessionKey');
    $this->metaPixel->shouldReceive('merge')->with(['some_event' => ['data' => ['param1' => 'value1'], 'event_id' => null]]);
    $this->metaPixel->shouldReceive('getFlashedEvent')->andReturn(['some_event' => ['data' => ['param1' => 'value1'], 'event_id' => null]]);

    $request = new Request();
    $response = $this->middleware->handle($request, function ($req) {
        return 'response';
    });

    expect($response)->toBe('response');
});

test('it flashes MetaPixel event data to the session', function () {
    $this->metaPixel->shouldReceive('sessionKey')->andReturn('pixelSessionKey');
    $this->metaPixel->shouldReceive('getFlashedEvent')->andReturn(['some_event' => ['data' => ['param1' => 'value1'], 'event_id' => null]]);

    $request = new Request();
    $response = $this->middleware->handle($request, function ($req) {
        return 'response';
    });

    expect($response)->toBe('response')
        ->and($this->session->get('pixelSessionKey'))->toEqual(['some_event' => ['data' => ['param1' => 'value1'], 'event_id' => null]]);
});
