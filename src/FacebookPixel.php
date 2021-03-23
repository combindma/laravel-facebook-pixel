<?php

namespace Combindma\FacebookPixel;

use Illuminate\Support\Traits\Macroable;

class FacebookPixel
{
    use Macroable;

    protected $enabled;
    protected $pixelId;
    protected $sessionKey;

    public function __construct()
    {
        $this->enabled = config('facebook-pixel.enabled');
        $this->setPixelId(config('facebook-pixel.facebook_pixel_id'));
        $this->setSessionKey(config('facebook-pixel.sessionKey'));
    }

    public function pixelId()
    {
        return $this->pixelId;
    }

    public function setPixelId($id): self
    {
        $this->pixelId = $id;
        return $this;
    }

    public function sessionKey()
    {
        return $this->sessionKey;
    }

    public function setSessionKey($sessionKey):self
    {
        $this->sessionKey = $sessionKey;
        return $this;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function headContent(): string
    {
        if (!$this->isEnabled()){
            return '';
        }

        $row = "
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '". $this->pixelId() ."', {
            em: '{customer_email}',
            ph: '{customer_phone}',
            });
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height=\"1\" width=\"1\" style=\"display:none\"
            src=\"https://www.facebook.com/tr?id=". $this->pixelId() ."&ev=PageView&noscript=1\"/>
        </noscript>";

        return $row;
    }

    public function bodyContent(): string
    {
        if ($this->isEnabled()){
            $facebookPixelSession = session()->pull($this->sessionKey(), []);
            $pixelCode = "";
            if (count($facebookPixelSession) > 0) {
                foreach ($facebookPixelSession as $key => $facebookPixel) {
                    $pixelCode .= "fbq('track', '" . $facebookPixel["name"] . "', " . json_encode($facebookPixel["parameters"]) . ");";
                }
                session()->forget($this->sessionKey());
                return "<script>" . $pixelCode . "</script>";
            }
        }
        return '';
    }

    public function createEvent($eventName, $parameters = []): void
    {
        $facebookPixelSession = session($this->sessionKey());
        $facebookPixelSession = !$facebookPixelSession ? [] : $facebookPixelSession;
        $facebookPixel = [
            "name"       => $eventName,
            "parameters" => $parameters,
        ];
        $facebookPixelSession[] = $facebookPixel;
        session([$this->sessionKey() => $facebookPixelSession]);
    }
}
