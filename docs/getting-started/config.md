# PHP Config File

All of the config settings available on the plugin's [Settings page](/getting-started/settings/) can also be managed via PHP in a config file. By setting these values in `config/google-maps.php`, they take precedence over whatever may be set in the control panel.

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

    // Required Google API keys
    'browserKey' => getenv('GOOGLEMAPS_BROWSERKEY'),
    'serverKey'  => getenv('GOOGLEMAPS_SERVERKEY'),

    // Optionally specify a geolocation service
    // Can be 'ipstack', 'maxmind', or null
    'geolocationService' => null,

    // If using ipstack
    'ipstackApiAccessKey' => getenv('IPSTACK_APIACCESSKEY'),

    // If using MaxMind
    'maxmindUserId'     => getenv('MAXMIND_USERID'),
    'maxmindLicenseKey' => getenv('MAXMIND_LICENSEKEY'),
    'maxmindService'    => getenv('MAXMIND_SERVICE'),

];
```
