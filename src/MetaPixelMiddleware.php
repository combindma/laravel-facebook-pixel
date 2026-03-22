<?php

namespace Combindma\FacebookPixel;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Store as Session;

class MetaPixelMiddleware
{
    public function __construct(protected MetaPixel $facebookPixel, protected Session $session) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $sessionKey = $this->facebookPixel->sessionKey();

        if ($this->session->has($sessionKey)) {
            $this->facebookPixel->merge($this->session->get($sessionKey, []));
        }

        $response = $next($request);

        $this->session->flash($sessionKey, $this->facebookPixel->getFlashedEvent());

        return $response;
    }
}
