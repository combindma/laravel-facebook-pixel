<?php

use Combindma\FacebookPixel\EventLayer;

beforeEach(function () {
    $this->eventLayer = new EventLayer;
});

test('it can set and retrieve event data', function () {
    $eventName = 'TestEvent';
    $parameters = ['param1' => 'value1'];
    $eventID = 'event123';

    $this->eventLayer->set($eventName, $parameters, $eventID);

    $expectedData = [
        [
            'event_name' => $eventName,
            'data' => $parameters,
            'event_id' => $eventID,
        ],
    ];

    expect($this->eventLayer->toArray())->toEqual($expectedData);
});

test('it can merge new data', function () {
    $initialData = [['event_name' => 'InitialEvent', 'data' => ['param1' => 'value1'], 'event_id' => 'event111']];
    $newData = [['event_name' => 'NewEvent', 'data' => ['param2' => 'value2'], 'event_id' => 'event222']];

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

test('it keeps multiple events with the same name in order', function () {
    $this->eventLayer->set('Purchase', ['value' => 30], 'first-event');
    $this->eventLayer->set('Purchase', ['value' => 40], 'second-event');

    expect($this->eventLayer->toArray())->toEqual([
        [
            'event_name' => 'Purchase',
            'data' => ['value' => 30],
            'event_id' => 'first-event',
        ],
        [
            'event_name' => 'Purchase',
            'data' => ['value' => 40],
            'event_id' => 'second-event',
        ],
    ]);
});

test('it normalizes legacy keyed event data during merge', function () {
    $this->eventLayer->merge([
        'Purchase' => [
            'data' => ['value' => 30],
            'event_id' => 'purchase-1',
        ],
    ]);

    expect($this->eventLayer->toArray())->toEqual([
        [
            'event_name' => 'Purchase',
            'data' => ['value' => 30],
            'event_id' => 'purchase-1',
        ],
    ]);
});
