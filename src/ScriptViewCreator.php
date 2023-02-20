<?php

namespace Combindma\FacebookPixel;

use Exception;
use Illuminate\View\View;

class ScriptViewCreator
{
    protected FacebookPixel $facebookPixel;

    public function __construct(FacebookPixel $facebookPixel)
    {
        $this->facebookPixel = $facebookPixel;
    }

    /**
     * @throws Exception
     */
    public function create(View $view): void
    {
        if ($this->facebookPixel->isEnabled() && empty($this->facebookPixel->pixelId())) {
            throw new Exception('You need to set a Facebook Pixel Id in .env file.');
        }

        if ($this->facebookPixel->isEnabled() && empty($this->facebookPixel->sessionKey())) {
            throw new Exception('You need to set a session key for Facebook Pixel in .env file.');
        }

        $view
            ->with('enabled', $this->facebookPixel->isEnabled())
            ->with('pixelId', $this->facebookPixel->pixelId())
            ->with('eventLayer', $this->facebookPixel->getEventLayer())
            ->with('customEventLayer', $this->facebookPixel->getCustomEventLayer())
            ->with('email', $this->facebookPixel->getEmail());
    }
}
