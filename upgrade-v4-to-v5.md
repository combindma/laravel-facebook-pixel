# Upgrade from v4 to v5

Update the package, then make these changes in your Laravel app:

1. **Configuration:** publish the new file with `php artisan vendor:publish --tag="meta-pixel-config"`. Copy your settings into `config/meta-pixel.php` and update old `config()` references. The Pixel ID key is now `pixel_id`; the session key is `session_key`.
2. **Environment variables:** rename the following entries in `.env`:

   | v4 | v5 |
   | --- | --- |
   | `FACEBOOK_PIXEL_ID` | `META_PIXEL_ID` |
   | `FACEBOOK_PIXEL_ENABLED` | `META_PIXEL_ENABLED` |
   | `FACEBOOK_PIXEL_TOKEN` | `META_PIXEL_TOKEN` |
   | `FACEBOOK_PIXEL_SESSION_KEY` | `META_PIXEL_SESSION_KEY` |
   | `FACEBOOK_TEST_EVENT_CODE` | `META_TEST_EVENT_CODE` |

3. **Facade and middleware:** replace old references with `Combindma\FacebookPixel\Facades\MetaPixel` and `Combindma\FacebookPixel\MetaPixelMiddleware`. For flashed events, keep the middleware after Laravel's session middleware.
4. **Blade layout:** replace `@include('facebookpixel::head')` and `@include('facebookpixel::body')` with the components below:

   ```blade
   <head>
       <x-metapixel-head />
   </head>
   <body>
       <x-metapixel-body />
       {{-- Page content --}}
   </body>
   ```

5. **Advanced matching:** the new `advanced_matching_enabled` setting defaults to `true`. Set `META_PIXEL_ADVANCED_MATCHING_ENABLED=false` to stop automatically including authenticated user details.

After updating, clear cached configuration with `php artisan config:clear`. Confirm `META_PIXEL_ENABLED=true` in environments where you want tracking, then check browser events and, if used, Conversions API events in Meta Events Manager.

See the [current documentation](README.md) for setup and examples.
