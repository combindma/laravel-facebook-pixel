<?php

return [
    /*
     * The Meta pixel id, should be a code that looks something like "1202417153106158".
     */
    'pixel_id' => env('META_PIXEL_ID', ''),

    /*
     * The key under which data is saved to the session with flash.
     */
    'session_key' => env('META_PIXEL_SESSION_KEY', config('app.name').'_metaPixel'),

    /*
     * Only if you plan using Conversions API for server events
     * To use the Conversions API, you need an access token. For Documentation please see: https://developers.facebook.com/docs/marketing-api/conversions-api/get-started
     */
    'token' => env('META_PIXEL_TOKEN', ''),

    /*
     * Enable or disable advanced matching. Useful for adjusting user privacy.
     */
    'advanced_matching_enabled' => env('META_PIXEL_ADVANCED_MATCHING_ENABLED', true),

    /*
     * Enable or disable script rendering. Useful for local development.
     */
    'enabled' => env('META_PIXEL_ENABLED', false),

    /*
     * This is used to test server events
     */
    'test_event_code' => env('META_TEST_EVENT_CODE'),
];
