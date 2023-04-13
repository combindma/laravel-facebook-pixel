<?php

use Combindma\FacebookPixel\FacebookPixel;
use Combindma\FacebookPixel\FacebookPixelMiddleware;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

beforeEach(function () {
    // Mock the FacebookPixel class
    $this->facebookPixel = Mockery::mock(FacebookPixel::class);
    $this->session = app(SessionManager::class)->driver('array');
    $this->middleware = new FacebookPixelMiddleware($this->facebookPixel, $this->session);
});

test('it merges session data with the FacebookPixel event layer', function () {
    $this->session->put('pixelSessionKey', ['some_event' => ['data' => ['param1' => 'value1'], 'event_id' => null]]);
    $this->facebookPixel->shouldReceive('sessionKey')->andReturn('pixelSessionKey');
    $this->facebookPixel->shouldReceive('merge')->with(['some_event' => ['data' => ['param1' => 'value1'], 'event_id' => null]]);
    $this->facebookPixel->shouldReceive('getFlashedEvent')->andReturn(['some_event' => ['data' => ['param1' => 'value1'], 'event_id' => null]]);

    $request = new Request();
    $response = $this->middleware->handle($request, function ($req) {
        return 'response';
    });

    expect($response)->toBe('response');
});

test('it flashes FacebookPixel event data to the session', function () {
    $this->facebookPixel->shouldReceive('sessionKey')->andReturn('pixelSessionKey');
    $this->facebookPixel->shouldReceive('getFlashedEvent')->andReturn(['some_event' => ['data' => ['param1' => 'value1'], 'event_id' => null]]);

    $request = new Request();
    $response = $this->middleware->handle($request, function ($req) {
        return 'response';
    });

    expect($response)->toBe('response')
        ->and($this->session->get('pixelSessionKey'))->toEqual(['some_event' => ['data' => ['param1' => 'value1'], 'event_id' => null]]);
});
