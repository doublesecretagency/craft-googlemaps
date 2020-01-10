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
];
```
