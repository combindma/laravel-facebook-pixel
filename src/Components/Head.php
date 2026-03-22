<?php

namespace Combindma\FacebookPixel\Components;

use Combindma\FacebookPixel\MetaPixel;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\View\Component;

class Head extends Component
{
    /**
     * @throws Exception
     */
    public function __construct(public MetaPixel $metaPixel, public bool $userIdAsString = false)
    {
        if ($this->metaPixel->isEnabled() && empty($this->metaPixel->pixelId())) {
            throw new Exception('You need to set a Meta Pixel Id in .env file.');
        }

        if ($this->metaPixel->isEnabled() && empty($this->metaPixel->sessionKey())) {
            throw new Exception('You need to set a session key for Meta Pixel in .env file.');
        }
    }

    public function render(): View|Factory|Application
    {
        return view('meta-pixel::head');
    }
}
