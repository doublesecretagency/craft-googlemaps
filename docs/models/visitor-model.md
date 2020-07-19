# Visitor Model

The properties and methods of the Visitor Model are identical whether you are accessing them via Twig or PHP.

:::code
```twig
{% set visitor = googleMaps.getVisitor() %}
```
```php
use doublesecretagency\googlemaps\helpers\GoogleMaps;

$visitor = GoogleMaps::getVisitor();
```
:::

In all cases, `visitor` will be a Visitor Model.

::: warning ADDITIONAL PROPERTIES AND METHODS
The Visitor Model is an extension of the [Location Model](/models/location-model/). It contains all properties and methods of the Location Model, plus the properties and methods shown below.

You can access `lat` and `lng` just as easily as `service` or `ip`.
:::

## Public Properties

### `service`

_string_ - The name of the service used to perform the geolocation.

### `ip`

_string_ - The IP address of the geolocation lookup.

### `city`

_string_ - The city determined by the geolocation lookup.

### `state`

_string_ - The state determined by the geolocation lookup.

### `country`

_string_ - The country determined by the geolocation lookup.

### `raw`

_array_ - The original data used to create this Visitor Model. Contains the full response from the original geolocation service.
