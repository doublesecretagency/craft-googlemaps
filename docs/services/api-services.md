# API Services

These service methods can be accessed like so...

```php
use doublesecretagency\googlemaps\GoogleMapsPlugin;

GoogleMapsPlugin::$plugin->api->methodName()
```

### `getServerKey()`

Get the server key stored for the Google Maps API.
 
#### Returns

_string_ - The Google Maps API server key.

### `getBrowserKey()`

Get the browser key stored for the Google Maps API.
 
#### Returns

_string_ - The Google Maps API browser key.

### `setServerKey()`

Change the server key stored for the Google Maps API.

#### Arguments

 - `$key` (_string_) - The new Google Maps API server key.

### `setBrowserKey()`

Change the browser key stored for the Google Maps API.

#### Arguments

 - `$key` (_string_) - The new Google Maps API browser key.
