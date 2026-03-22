<?php

namespace Combindma\FacebookPixel\Components;

use Combindma\FacebookPixel\MetaPixel;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\View\Component;

class Body extends Component
{
    public array $eventLayer;

    public array $customEventLayer;

    public function __construct(public MetaPixel $metaPixel)
    {
        $this->eventLayer = $this->metaPixel->getEventLayer()->toArray();
        $this->customEventLayer = $this->metaPixel->getCustomEventLayer()->toArray();
    }

    public function render(): View|Factory|Application
    {
        return view('meta-pixel::body');
    }
}
