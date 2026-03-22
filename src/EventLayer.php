<?php

namespace Combindma\FacebookPixel;

class EventLayer
{
    protected array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $this->normalize($data);
    }

    public function set(string $eventName, array $parameters = [], ?string $eventId = null): void
    {
        $this->data[] = [
            'event_name' => $eventName,
            'data' => $parameters,
            'event_id' => $eventId,
        ];
    }

    public function merge(array $newData): void
    {
        $this->data = [...$this->data, ...$this->normalize($newData)];
    }

    public function clear(): void
    {
        $this->data = [];
    }

    public function toArray(): array
    {
        return $this->data;
    }

    protected function normalize(array $events): array
    {
        $normalizedEvents = [];

        foreach ($events as $eventName => $event) {
            if (! is_array($event)) {
                continue;
            }

            if (is_int($eventName)) {
                $normalizedEventName = $event['event_name'] ?? null;

                if (! is_string($normalizedEventName) || $normalizedEventName === '') {
                    continue;
                }

                $normalizedEvents[] = [
                    'event_name' => $normalizedEventName,
                    'data' => is_array($event['data'] ?? null) ? $event['data'] : [],
                    'event_id' => is_string($event['event_id'] ?? null) ? $event['event_id'] : null,
                ];

                continue;
            }

            $normalizedEvents[] = [
                'event_name' => $eventName,
                'data' => is_array($event['data'] ?? null) ? $event['data'] : [],
                'event_id' => is_string($event['event_id'] ?? null) ? $event['event_id'] : null,
            ];
        }

        return $normalizedEvents;
    }
}
