# v5 Migration Guide: Steps to Upgrade from v4

Migrating to the latest version of our package is straightforward and involves a few key changes to ensure compatibility and take advantage of new features. Please follow the steps below to complete your migration.

## Configuration Changes

### 1. Config File Rename
The configuration file has been renamed to `meta-pixel`. Please update any references to the old config file in your project.

### 2. Environment Variable Prefix Change
Replace the `FACEBOOK_` prefix in your environment variables with `META_`. For example, change `FACEBOOK_PIXEL_ID` to `META_PIXEL_ID`.

## Feature Additions

### 3. Advanced Matching Enabled
We have added a new feature `advanced_matching_enabled` to the configuration file, which is enabled by default. This feature enhances user tracking accuracy.

## Middleware Update

### 4. Middleware Name Update
The middleware name has been updated to `MetaPixelMiddleware`. Update your middleware references accordingly.

## Facade Updates

### 5. Facade Call Changes
All facade calls should now be made to `MetaPixel` instead of the previous facade name.

## Syntax Updates in HTML

### 6. Head and Body Tag Names
Update the head and body tags in your HTML files using the new syntax for better integration:

```html
<!DOCTYPE html>
<html>
<head>
    <x-metapixel-head/>
</head>
<body>
    <x-metapixel-body/>
</body>
</html>
``` 

