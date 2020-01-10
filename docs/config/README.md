# Config File

There are a few configuration values which will need to be hard-coded in a config file. You can find an example of the config file here...

```
/vendor/doublesecretagency/craft-googlemaps/config.php
```

Copy, rename, and move that file to here...

```
/config/google-maps.php
```

This file, like your `db.php` and `general.php`, is [environmentally aware](https://docs.craftcms.com/v3/config/environments.html#config-files). You can also pass in environment values using the `getenv` PHP method.

```php
return [
    'serverKey' => getenv('GOOGLEMAPS_SERVERKEY'),
    'browserKey' => getenv('GOOGLEMAPS_BROWSERKEY')
    // Include geolocation service credentials (optional)
];
```

## Optional Geolocation Services

If you are using a third-party visitor geolocation service, then you will want to specify those credentials as well. The credentials you include depend entirely on which geolocation service you are using.

First, determine which third-party geolocation service you will be using...

```php
// Can be 'ipstack', 'maxmind', or null
'geolocationService' => 'ipstack',
```

Then add the credentials for the specified geolocation service...

### ipstack

```php
'ipstackApiAccessKey' => getenv('IPSTACK_APIACCESSKEY')
```

### MaxMind

```php
'maxmindService' => getenv('MAXMIND_SERVICE'),
'maxmindUserId' => getenv('MAXMIND_USERID'),
'maxmindLicenseKey' => getenv('MAXMIND_LICENSEKEY')
```
