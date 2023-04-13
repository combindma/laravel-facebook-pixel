<?php

use Combindma\FacebookPixel\EventLayer;

beforeEach(function () {
    $this->eventLayer = new EventLayer();
});

test('it can set and retrieve event data', function () {
    $eventName = 'TestEvent';
    $parameters = ['param1' => 'value1'];
    $eventID = 'event123';

    $this->eventLayer->set($eventName, $parameters, $eventID);

    $expectedData = [
        $eventName => [
            'data' => $parameters,
            'event_id' => $eventID,
        ],
    ];

    expect($this->eventLayer->toArray())->toEqual($expectedData);
});

test('it can merge new data', function () {
    $initialData = ['InitialEvent' => ['data' => ['param1' => 'value1'], 'event_id' => 'event111']];
    $newData = ['NewEvent' => ['data' => ['param2' => 'value2'], 'event_id' => 'event222']];

    $this->eventLayer = new EventLayer($initialData);
    $this->eventLayer->merge($newData);

    $expectedData = array_merge($initialData, $newData);
    expect($this->eventLayer->toArray())->toEqual($expectedData);
});

test('it can clear event data', function () {
    $eventName = 'TestEvent';
    $parameters = ['param1' => 'value1'];
    $eventID = 'event123';

    $this->eventLayer->set($eventName, $parameters, $eventID);
    $this->eventLayer->clear();

    expect($this->eventLayer->toArray())->toBeEmpty();
});
