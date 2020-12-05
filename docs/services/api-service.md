# API Service

These service methods can be accessed like so...

```php
use doublesecretagency\googlemaps\GoogleMapsPlugin;

GoogleMapsPlugin::$plugin->api->...
```

### `getBrowserKey()`

Get the browser key stored for the Google Maps API.
 
#### Returns

_string_ - The Google Maps API browser key.

### `getServerKey()`

Get the server key stored for the Google Maps API.
 
#### Returns

_string_ - The Google Maps API server key.

### `setBrowserKey($key)`

Change the browser key stored for the Google Maps API.

#### Arguments

 - `$key` (_string_) - The new Google Maps API browser key.
 
### `setServerKey($key)`

Change the server key stored for the Google Maps API.

#### Arguments

 - `$key` (_string_) - The new Google Maps API server key.

### `getApiUrl($params = [])`

Get the URL used internally for pinging the Google Maps API.

#### Arguments

 - `$params` (_array_) - A set of optional parameters to append to the URL.
 
#### Returns

_string_ - The Google Maps JavaScript API URL.
