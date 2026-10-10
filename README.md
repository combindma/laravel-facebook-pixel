# Meta Pixel integration for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/combindma/laravel-facebook-pixel.svg?style=flat-square)](https://packagist.org/packages/combindma/laravel-facebook-pixel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/combindma/laravel-facebook-pixel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/combindma/laravel-facebook-pixel/actions?query=workflow%3ATests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/combindma/laravel-facebook-pixel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/combindma/laravel-facebook-pixel/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/combindma/laravel-facebook-pixel.svg?style=flat-square)](https://packagist.org/packages/combindma/laravel-facebook-pixel)

Track events from the browser with **Meta Pixel**, from your server with the **Conversions API**, or both.

Requires PHP 8.3+ and Laravel 11, 12, or 13. Upgrading? See the [v4 → v5 guide](upgrade-v4-to-v5.md). For older installations, see the [v4 docs](README-v4.md).

- [Quick start](#quick-start)
- [Browser events](#browser-events)
- [Conversions API](#conversions-api)
- [Configuration and helpers](#configuration-and-helpers)
- [Development](#development)

## Quick start

### 1. Install

Create a [Meta Pixel](https://web.facebook.com/business/help/952192354843755), then install the package:

```bash
composer require combindma/laravel-facebook-pixel
```

### 2. Enable tracking

Add your Pixel ID to `.env`:

```dotenv
META_PIXEL_ID=your-pixel-id
META_PIXEL_ENABLED=true
```

Tracking is disabled by default. Setting `META_PIXEL_ENABLED=false` disables both browser scripts and Conversions API requests.

### 3. Add the Blade components

Place these components in your shared layout. The head component automatically tracks `PageView`; the body component renders events added in your controller.

```blade
<!DOCTYPE html>
<html>
<head>
    <x-metapixel-head />
</head>
<body>
    <x-metapixel-body />
    @yield('content')
</body>
</html>
```

### 4. Track an event

In your controller, before returning the view:

```php
use Combindma\FacebookPixel\Facades\MetaPixel;

MetaPixel::track('Purchase', ['currency' => 'USD', 'value' => 30.00]);

return view('thank-you');
```

## Browser events

Use `track()` for standard Meta events and `trackCustom()` for your own events. Parameters and the third argument (event ID) are optional.

```php
use Combindma\FacebookPixel\Facades\MetaPixel;

MetaPixel::track('AddToCart', ['currency' => 'USD', 'value' => 30.00], 'cart-line-1');
MetaPixel::trackCustom('NewsletterSignup', ['source' => 'footer']);
```

Repeated calls with the same event name preserve every event in insertion order.

### Track after a redirect

Use `flashEvent()` to render a standard event on the next request. Custom events cannot be flashed.

First, add the middleware to the relevant routes in `routes/web.php`, **after Laravel's session middleware**. Apply it to both the route that redirects and the destination route:

```php
use Combindma\FacebookPixel\MetaPixelMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(MetaPixelMiddleware::class)->group(function () {
    // Your form and thank-you routes...
});
```

Then, in your controller:

```php
use Combindma\FacebookPixel\Facades\MetaPixel;

MetaPixel::flashEvent('Lead', ['content_name' => 'Contact form']);

return redirect('/thank-you');
```

`flashEvent()` accepts the same parameters and optional event ID as `track()`, and preserves repeated events.

## Conversions API

Complete the quick start, then generate an access token using [Meta's setup guide](https://developers.facebook.com/docs/marketing-api/conversions-api/get-started). Add it to `.env`:

```dotenv
META_PIXEL_TOKEN=your-access-token
# Optional: enables appsecret_proof for API requests.
META_PIXEL_APP_SECRET=your-app-secret
```

Find the optional app secret in **Meta App Dashboard → Settings → Basic**. Keep these credentials on the server.

### Send browser and server events together

Use `trackAndSend()` to share one event ID across both channels so Meta can deduplicate the event. It generates an ID unless you provide one:

```php
use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Object\ServerSide\CustomData;

$customData = (new CustomData())
    ->setCurrency('USD')
    ->setValue(30.00);

MetaPixel::trackAndSend(
    'Purchase',
    ['currency' => 'USD', 'value' => 30.00],
    $customData,
    eventId: 'order-123',
);
```

Use a unique ID for each event, and reuse it only for the browser/server copies of that event. The browser copy requires a response that renders the Blade components; for redirects, use `flashEvent()` and `send()` with the same ID instead.

### Send a server-only event

Using the `$customData` above:

```php
MetaPixel::send('Purchase', 'order-123', $customData);
```

By default, server events include the request IP, user agent, and `_fbc`/`_fbp` cookies. With advanced matching enabled, they also include the authenticated user's email and ID when both are available.

To supply customer details, build on `userData()` and pass it as `userData` to either `send()` or `trackAndSend()`:

```php
$userData = MetaPixel::userData()
    ->setEmail('customer@example.com')
    ->setPhone('12345678901');

MetaPixel::send('Purchase', 'order-123', $customData, userData: $userData);
```

For product details, add SDK `Content` objects with `$customData->setContents([...])`. See Meta's [Payload Helper](https://developers.facebook.com/docs/marketing-api/conversions-api/payload-helper) for event fields.

### Test server events

Set `META_TEST_EVENT_CODE` in `.env` to the code from Meta Events Manager's **Test Events** tab. It is included in every API request. **Remove it when testing is finished.**

## Configuration and helpers

Advanced matching is enabled by default: it adds the authenticated user's email and ID to browser and server events when both are available. Disable it with `META_PIXEL_ADVANCED_MATCHING_ENABLED=false`.

| Environment variable | Purpose / default |
| --- | --- |
| `META_PIXEL_ID` | Your Pixel ID; empty by default |
| `META_PIXEL_ENABLED` | Enable browser scripts and server requests; `false` |
| `META_PIXEL_TOKEN` | Conversions API access token; empty by default |
| `META_PIXEL_APP_SECRET` | Optional app secret for `appsecret_proof` |
| `META_PIXEL_ADVANCED_MATCHING_ENABLED` | Include authenticated user details; `true` |
| `META_PIXEL_SESSION_KEY` | Flashed-event session key; app name + `_metaPixel` |
| `META_PIXEL_LOGGING` | API request/error logging; `false` |
| `META_TEST_EVENT_CODE` | Server test-event code; unset by default |

Publish the [configuration](config/meta-pixel.php) or views only if you need to customize them:

```bash
php artisan vendor:publish --tag="meta-pixel-config"
php artisan vendor:publish --tag="meta-pixel-views"
```

Runtime helpers (using the `MetaPixel` facade):

| Method | Purpose |
| --- | --- |
| `pixelId()` / `setPixelId($id)` | Read / change the Pixel ID |
| `setToken($token)` | Change the API token |
| `setTestEventCode($code)` | Set the server test-event code |
| `isEnabled()` / `enable()` / `disable()` | Check / change tracking status |
| `clear()` | Clear standard browser events for the current request |

You can define reusable event methods with macros. Register them in a service provider's `boot()` method:

```php
MetaPixel::macro('purchase', function ($amount) {
    MetaPixel::track('Purchase', ['currency' => 'USD', 'value' => $amount]);
});

// In your controller:
MetaPixel::purchase(30.00);
```

## Development

Run tests with `composer test`. Contributions are welcome; include tests for behavior changes and bug fixes. Report vulnerabilities through the [security policy](../../security/policy).

Built by [Combind](https://combind.ma) and [contributors](../../contributors). Need help with a project? [Contact us](mailto:hello@combind.ma).

Licensed under the [MIT License](LICENSE.md).
