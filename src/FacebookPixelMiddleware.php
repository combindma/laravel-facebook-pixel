<?php

namespace Combindma\FacebookPixel;

use Closure;
use Illuminate\Session\Store as Session;

class FacebookPixelMiddleware
{
    protected $facebookPixel;

    protected $session;

    public function __construct(FacebookPixel $facebookPixel, Session $session)
    {
        $this->facebookPixel = $facebookPixel;
        $this->session = $session;
    }

    public function handle($request, Closure $next)
    {
        if ($this->session->has($this->facebookPixel->sessionKey())) {
            $this->facebookPixel->merge($this->session->get($this->facebookPixel->sessionKey()));
        }
        $response = $next($request);

        $this->session->flash($this->facebookPixel->sessionKey(), $this->facebookPixel->getFlashedEvent());

        return $response;
    }
}
