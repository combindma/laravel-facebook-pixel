# Facebook Pixel integration for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/combindma/laravel-facebook-pixel.svg?style=flat-square)](https://packagist.org/packages/combindma/laravel-facebook-pixel)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/combindma/laravel-facebook-pixel/run-tests?label=tests)](https://github.com/combindma/laravel-facebook-pixel/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/combindma/laravel-facebook-pixel/Check%20&%20fix%20styling?label=code%20style)](https://github.com/combindma/laravel-facebook-pixel/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/combindma/laravel-facebook-pixel.svg?style=flat-square)](https://packagist.org/packages/combindma/laravel-facebook-pixel)

An easy Facebook Pixel implementation for your Laravel application.

## Installation

You can install the package via composer:

```bash
composer require combindma/laravel-facebook-pixel
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Combindma\FacebookPixel\FacebookPixelServiceProvider" --tag="facebook-pixel-config"
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
     * Enable or disable script rendering. Useful for local development.
     */
    'enabled' => env('FACEBOOK_PIXEL_ENABLED', false),
];
```

## Usage

### Embed in Blade

Insert facebookPixelHead() helper after opening head tag, and facebookPixelBody() after opening body tag

```html
<!DOCTYPE html>
<html>
<head>
    {!! facebookPixelHead() !!}
</head>
<body>
{!! facebookPixelBody() !!}
</body>
```

### Send Pixel Event
```php
facebookPixel()->createEvent($eventName, $data);
//Or
\FacebookPixel::createEvent($eventName, $data);
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Combind](https://github.com/Combindma)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
