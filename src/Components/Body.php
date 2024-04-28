<?php

namespace Combindma\FacebookPixel\Components;

use Combindma\FacebookPixel\MetaPixel;
use Illuminate\View\Component;

class Body extends Component
{
    public array $eventLayer;

    public array $customEventLayer;

    public MetaPixel $metaPixel;

    public function __construct(MetaPixel $metaPixel)
    {
        $this->metaPixel = $metaPixel;
        $this->eventLayer = $this->metaPixel->getEventLayer()->toArray();
        $this->customEventLayer = $this->metaPixel->getCustomEventLayer()->toArray();
    }

    public function render()
    {
        return view('meta-pixel::body');
    }
}
