---
description: Using a PHP config file, you can override several of the plugin's settings. Find out how to configure the plugin, even across different environments!
---

# PHP Config File

All config settings available on the plugin's [Settings page](/getting-started/settings/) can also be managed via PHP in a config file. By setting these values in `config/google-maps.php`, they take precedence over whatever may be set in the control panel.

```shell
# Copy this file...
/vendor/doublesecretagency/craft-googlemaps/src/config.php

# To here... (and rename it)
/config/google-maps.php
```

Much like the `db.php` and `general.php` files, `google-maps.php` is [environmentally aware](https://craftcms.com/docs/3.x/config/#multi-environment-configs). You can also pass in environment values using the `getenv` PHP method.

:::warning Optional Geolocation Services
If you are using a third-party visitor geolocation service, then you may want to specify those credentials as well. The credentials you include depend entirely on which geolocation service you are using.
:::

```php
return [

    // Google Maps API keys (required)
    'browserKey' => getenv('GOOGLEMAPS_BROWSERKEY'),
    'serverKey'  => getenv('GOOGLEMAPS_SERVERKEY'),

    // Optional geolocation service ('ipstack', 'maxmind', or null)
    'geolocationService' => null,

    // ipstack (only needed if using the ipstack service)
    'ipstackApiAccessKey' => getenv('IPSTACK_APIACCESSKEY'),

    // MaxMind (only needed if using the MaxMind service)
    'maxmindUserId'     => getenv('MAXMIND_USERID'),
    'maxmindLicenseKey' => getenv('MAXMIND_LICENSEKEY'),
    'maxmindService'    => getenv('MAXMIND_SERVICE'),

    // Whether to log JS progress to the console (when a map is rendered)
    'enableJsLogging' => true,

    // Control the size of map UI elements in Address fields
    'fieldControlSize' => 27,

    // Additional optional parameters for configuring Address fields
    'fieldParams' => []

];
```

## Settings available via Control Panel

All settings pertaining to API keys and 3rd party geolocation services are also available via the control panel. For more information, consult the documentation regarding the [Settings](/getting-started/settings/) page.

You may also want to learn more about managing your [Google API keys](/getting-started/api-keys/), and how to optionally use a [3rd party geolocation service](/geolocation/service-providers/).

## Settings available only via PHP file

Beyond the API keys and 3rd party geolocation services, all other settings can only be managed via the PHP config file.

### `enableJsLogging`

_bool_ - Defaults to `true`.

Whether to [log dynamic map progress](/dynamic-maps/troubleshooting/) to the JavaScript console when the site is in `devMode`.

```php
// Prevent dynamic maps from logging to the console
'enableJsLogging' => false
```

### `fieldControlSize`

_int_ - Defaults to `27`.

Set the size of map UI elements (for all Address fields in the control panel).

For more details, see the official Google docs for the [`controlSize` option](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions.controlSize).

```php
// Make the map UI elements larger for all Address fields
'fieldControlSize' => 50
```

:::tip Only applies to Address fields in the Control Panel
This does not apply to maps displayed on the front-end of your site. Unless otherwise overridden, Google will continue to set that value to `40` by default.
:::

### `fieldParams`

_array_ - Defaults to an empty array.

Add parameters to the internal Google Maps API URL (for all Address fields in the control panel).

There are many reasons why you may need to append additional parameters to the API URL. The most common usage is for [localizing the Address maps](https://developers.google.com/maps/documentation/javascript/localization).

```php
// Set the language and region biasing for all Address fields
'fieldParams' => [
    'language' => 'ja',
    'region' => 'JP',
]
```

:::tip Only applies to Address fields in the Control Panel
If you need to change the language or apply region biasing to your _front-end_ maps, see the [Changing the Map Language](/guides/changing-map-language/) or [Region Biasing](/guides/region-biasing/) guides, respectively.
:::
