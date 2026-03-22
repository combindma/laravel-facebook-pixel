<?php

use Combindma\FacebookPixel\MetaPixel;

it('renders tracked and custom events in the body view', function () {
    $metaPixel = new MetaPixel;
    $metaPixel->track('Purchase', ['currency' => 'USD', 'value' => 30], 'purchase-1');
    $metaPixel->track('Purchase', ['currency' => 'USD', 'value' => 40], 'purchase-2');
    $metaPixel->trackCustom('LeadCaptured', ['source' => 'footer']);

    $html = view('meta-pixel::body', [
        'metaPixel' => $metaPixel,
        'eventLayer' => $metaPixel->getEventLayer()->toArray(),
        'customEventLayer' => $metaPixel->getCustomEventLayer()->toArray(),
    ])->render();

    expect($html)
        ->toContain("fbq('track', 'Purchase'")
        ->toContain("eventID: 'purchase-1'")
        ->toContain("eventID: 'purchase-2'")
        ->toContain("fbq('trackCustom', 'LeadCaptured'");
});

it('does not render events when the pixel is disabled', function () {
    $metaPixel = new MetaPixel;
    $metaPixel->disable();
    $metaPixel->track('Purchase', ['currency' => 'USD', 'value' => 30]);

    $html = view('meta-pixel::body', [
        'metaPixel' => $metaPixel,
        'eventLayer' => $metaPixel->getEventLayer()->toArray(),
        'customEventLayer' => $metaPixel->getCustomEventLayer()->toArray(),
    ])->render();

    expect(trim($html))->toBe('');
});
