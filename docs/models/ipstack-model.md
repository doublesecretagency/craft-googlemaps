# Ipstack Model

The Ipstack Model interacts with the [ipstack geolocation service](https://ipstack.com/). You can subscribe to ipstack and use their services to perform geolocation lookups on your visitors.

:::warning FOR EDGE CASES ONLY
You will rarely need to call this model directly. If you have ipstack configured as your default geolocation service, then the preferred method is to perform geolocation normally.

Read how to properly manage [Visitor Geolocation...](/geolocation/)
:::

## Public Methods

### `geolocate()`

```php
use doublesecretagency\googlemaps\models\Ipstack;

$visitor = Ipstack::geolocate($ipAddress);
```

#### Arguments

 - `$ip` (_string_) - An IP address on which to perform the geolocation.
 - `$parameters` (_array_) - An optional configuration array of [GET parameters](https://ipstack.com/documentation).

#### Returns

_Visitor_ - A [Visitor Model](/models/visitor-model/) containing the approximate location of a visitor.
