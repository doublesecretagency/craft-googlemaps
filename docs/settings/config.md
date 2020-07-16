# PHP Config File

All of the config settings available on the plugin's [Settings page](/settings/) can also be managed via PHP in a config file. By setting these values in `config/google-maps.php`, they take precedence over whatever may be set in the control panel.

```shell
# Copy this file...
/vendor/doublesecretagency/craft-googlemaps/config.php

# To here... (and rename it)
/config/google-maps.php
```

Much like the `db.php` and `general.php` files, `google-maps.php` is [environmentally aware](https://docs.craftcms.com/v3/config/environments.html#config-files). You can also pass in environment values using the `getenv` PHP method.

:::warning OPTIONAL GEOLOCATION SERVICES
If you are using a third-party visitor geolocation service, then you may want to specify those credentials as well. The credentials you include depend entirely on which geolocation service you are using.
:::

```php
return [

    // Required Google API keys
    'googleApiServerKey' => getenv('GOOGLEAPI_SERVERKEY'),
    'googleApiBrowserKey' => getenv('GOOGLEAPI_BROWSERKEY'),

    // Optionally specify a geolocation service
    // Can be 'ipstack', 'maxmind', or null
    'geolocationService' => null,

    // If using ipstack
    'ipstackApiAccessKey' => getenv('IPSTACK_APIACCESSKEY'),

    // If using MaxMind
    'maxmindService' => getenv('MAXMIND_SERVICE'),
    'maxmindUserId' => getenv('MAXMIND_USERID'),
    'maxmindLicenseKey' => getenv('MAXMIND_LICENSEKEY')

];
```
