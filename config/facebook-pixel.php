<?php

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

    /*
     * This is used to test server events
     */
    'test_event_code' => env('FACEBOOK_TEST_EVENT_CODE'),
];
