# Facebook Pixel integration for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/combindma/laravel-facebook-pixel.svg?style=flat-square)](https://packagist.org/packages/combindma/laravel-facebook-pixel)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/combindma/laravel-facebook-pixel/run-tests?label=tests)](https://github.com/combindma/laravel-facebook-pixel/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/combindma/laravel-facebook-pixel/Check%20&%20fix%20styling?label=code%20style)](https://github.com/combindma/laravel-facebook-pixel/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/combindma/laravel-facebook-pixel.svg?style=flat-square)](https://packagist.org/packages/combindma/laravel-facebook-pixel)

A Complete Facebook Pixel implementation for your Laravel application.

## Installation

You can install the package via composer:

```bash
composer require combindma/laravel-facebook-pixel
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="facebook-pixel-config"
```

This is the contents of the published config file:

```php
return [
   /*
     * The Facebook Pixel id, should be a code that looks something like "XXXXXXXXXXXXXXXX".
     */
    'facebook_pixel_id' => env('FACEBOOK_PIXEL_ID', ''),

    /*
     * The key under which data is saved to the session with flash.
     */
    'sessionKey' => env('FACEBOOK_PIXEL_SESSION_KEY', config('app.name').'_facebookPixel'),

    /*
     * To use the Conversions API, you need an access token. For Documentation please see: https://developers.facebook.com/docs/marketing-api/conversions-api/get-started
     */
    'token' => env('FACEBOOK_PIXEL_TOKEN', ''), //Only if you plan using Conversions API for server events

    /*
     * Enable or disable script rendering. Useful for local development.
     */
    'enabled' => env('FACEBOOK_PIXEL_ENABLED', false),
];
```

If you plan on using the [flash-functionality](#flashing-data-for-the-next-request) you must install the FacebookPixelMiddleware, after the StartSession middleware:

```php
// app/Http/Kernel.php
protected $middleware = [
    ...
    \Illuminate\Session\Middleware\StartSession::class,
    \Combindma\FacebookPixel\FacebookPixelMiddleware::class,
    ...
];
``` 

## Usage

### Include scripts in Blade

Insert head view after opening head tag, and body view after opening body tag

```html
<!DOCTYPE html>
<html>
<head>
    @include('facebookpixel::head')
</head>
<body>
    @include('facebookpixel::body')
</body>
```

Your events will also be rendered here. To add an event, use the `track()` function.

```php
// HomeController.php
use Combindma\FacebookPixel\Facades\FacebookPixel;

public function index()
{
    FacebookPixel::track('Purchase', ['currency' => 'USD', 'value' => 30.00]);
    return view('home');
}
```

This renders:

```html
<html>
  <head>
    <script>/* Facebook Pixel's base script */</script>
    <!-- ... -->
  </head>
  <body>
  <script>fbq('track', 'Purchase', {"currency":"USD","value":30});</script>
  <!-- ... -->
</html>
```

#### Flashing data for the next request

The package can also set event to render on the next request. This is useful for setting data after an internal redirect.

```php
// ContactController.php
use Combindma\FacebookPixel\Facades\FacebookPixel;

public function postContact()
{
    // Do contact form stuff...
    FacebookPixel::flashEvent('Lead', [
        'content_name' => 'Auto Insurance',
        'content_category' => 'Quote',
        'value' => 40.00,
        'currency' => 'USD'
    ]);
    return redirect()->action('ContactController@getContact');
}
```

After a form submit, the following event will be parsed on the contact page:

```html
<html>
<head>
    <script>/* Facebook Pixel's base script */</script>
    <!-- ... -->
</head>
<body>
<script>
    fbq(
        'track', 'Lead', {
            'content_name': 'Auto Insurance',
            'content_category': 'Quote',
            'value': 40.00,
            'currency': 'USD'
        }
    );
</script>
<!-- ... -->
</html>
```

### Other Simple Methods

```php
use Combindma\FacebookPixel\Facades\FacebookPixel;

// Retrieve your Pixel id
$id = FacebookPixel::pixelId();
// Set Pixel id on the fly
$id = FacebookPixel::setPixelId('XXXXXXXX');
// Check whether script rendering is enabled
$enabled = FacebookPixel::isEnabled(); // true|false
// Enable and disable script rendering on the fly
FacebookPixel::enable();
FacebookPixel::disable();
// Add event to the event layer (automatically renders right before the pixel script). Setting new values merges them with the previous ones.
FacebookPixel::track('eventName', ['attribute' => 'value']);
FacebookPixel::track('eventName'); //without properties 
// Flash event for the next request. Setting new values merges them with the previous ones.
FacebookPixel::flashEvent('eventName', ['attribute' => 'value']);
FacebookPixel::flashEvent('eventName'); //without properties
//Clear the event layer.
FacebookPixel::clear();
```

### Custom Events

You can also track a specific custom event on your website. This feature is not available for flashed events.

```php
use Combindma\FacebookPixel\Facades\FacebookPixel;

// In your controller
FacebookPixel::trackCustom('CUSTOM-EVENT-NAME', ['custom_parameter' => 'ABC', 'value' => 10.00, 'currency' => 'USD']);
```

This renders:

```html
<html>
  <head>
    <script>/* Facebook Pixel's base script */</script>
    <!-- ... -->
  </head>
  <body>
  <script>
      fbq(
          'trackCustom', 'CUSTOM-EVENT-NAME', {
              'custom_parameter': 'ABC',
              'value': 10.00,
              'currency': 'USD'
          }
      );
  </script>
  <!-- ... -->
</html>
```

### Advanced matching

This package provides by default advanced matching. We retrieve the email from authenticated user and include it in the Pixel base code fbq('init') function call as a third parameter.

```html
<html>
<head>
    <script>
        /* Facebook Pixel's base script */
        <!-- ... -->
        fbq('init', '{PixelID}', {
            em: 'email@email.com', //Email provided by Auth::user()->email
        });
    </script>
    <!-- ... -->
</head>
<body>
<!-- ... -->
</html>
```

### Macroable

Adding events to pages can become a repetitive process. Since this package isn't supposed to be opinionated on what your events should look like, the FacebookPixel is macroable.

```php
use Combindma\FacebookPixel\Facades\FacebookPixel;

//include this in your macrobale file
FacebookPixel::macro('purchase', function ($product) {
    FacebookPixel::track('Purchase', [
        'currency' => 'EUR',
        'value' => $product->price
    ]);
});

//in your controller
FacebookPixel::purchase($product);
```

### Conversions API

If you plan on using [Conversions API](https://developers.facebook.com/docs/marketing-api/conversions-api/get-started) functionalities. This is how you can start:

```php
use Combindma\FacebookPixel\Facades\FacebookPixel;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\UserData;

//in your controller file
$user_data = (new UserData())
    ->setEmails(array('joe@eg.com'))
    ->setPhones(array('12345678901', '14251234567'))
    // It is recommended to send Client IP and User Agent for Conversions API Events.
    ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
    ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
    ->setFbc('fb.1.1554763741205.AbCdEfGhIjKlMnOpQrStUvWxYz1234567890')
    ->setFbp('fb.1.1558571054389.1098115397');

$content = (new Content())
    ->setProductId('product123')
    ->setQuantity(1)
    ->setDeliveryCategory(DeliveryCategory::HOME_DELIVERY);
    
$custom_data = (new CustomData())
    ->setContents(array($content))
    ->setCurrency('usd')
    ->setValue(123.45);
    
//send request
FacebookPixel::send('Purchase', 'http://jaspers-market.com/product/123', $user_data, $custom_data);
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Combind](https://github.com/Combindma)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
